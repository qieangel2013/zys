<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2018
|---------------------------------------------------------------
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2018/1/4
|---------------------------------------------------------------
*/

class MySQLPollServer
{
    public static $instance;
    private $sqlconfig;
    private $redisconfig;
    private $busy_pool_size;
    private $pool_idel;
    private $redisinstance;
    private $poolsize_max=10;//申请最大的mysql异步客户端
    private $poolreease_max=1000;//sql的存储队列里最大数会自动扩容
    private $dilatationpool;
    private $dilatationpool_max=10;//自动扩容的最大mysql异步客户端
    private $timetick=5000;//定时多长时间检测未处理的sql,注意这是毫秒
    private $tasksql_max=100000;
    private $taskcount;
    private $lock;
    private $server;
    private $countquerysql;
    private $msg='';
    private $msgtable;
    public function __construct()
    {
        define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
        define('MYPATH', dirname(APPLICATION_PATH));
        $this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
        $this->application->bootstrap();
        $config_obj=Yaf_Registry::get("config");
        $databaseconfig=$config_obj->database->config->toArray();
        $syncmysql=$config_obj->syncmysql->toArray();
        $redisconfig=$config_obj->redis->config->toArray();
        $this->server = new swoole_server($syncmysql['ServerIp'], $syncmysql['port']);
        $this->sqlconfig = array(
            'host' => $databaseconfig['host'],
            'user' => $databaseconfig['user'],
            'password' => $databaseconfig['pwd'],
            'database' => $databaseconfig['name'],
            'charset' => 'utf8'
        );
        $tickobj=$this;
       $this->redisconfig=array(
            'host' =>$redisconfig['server'], 
            'port' =>$redisconfig['port']
            );
        //建立锁机制
        $this->lock = new swoole_lock(SWOOLE_MUTEX);

        //创建mysql统计内存表
        $this->busy_pool_size = new swoole_atomic();

        //创建自动扩容mysql统计内存表
        $this->dilatationpool = new swoole_atomic();

        //创建自动扩容mysql的sql执行统计内存表
        $this->taskcount = new swoole_atomic();

        //创建统计sql存储长度
        $this->countquerysql = new swoole_atomic();

        //创建存储错误信息
        $this->msgtable = new swoole_table(32);
        $this->msgtable->column('error', swoole_table::TYPE_STRING,100);
        $this->msgtable->create();
        $errordata=array(
                'error' =>''
        );
        $this->msgtable->set('errno',$errordata);
        try{
            //创建存储sql的内存表
            $this->redisinstance = new Redis();
            $this->redisinstance->pconnect($this->redisconfig['host'], $this->redisconfig['port']);
        }catch(Exception $e){
            $errordata=array(
                'error' =>'RedisServer '. $e->getMessage()
                );
            $this->msgtable->set('errno',$errordata);
        }
        
        if (isset($config['logfile'])) {
            $this->server->set(
                array(
                    'worker_num'  => 4,
                    'daemonize' => true,
                    'buffer_output_size' => 500 * 1024 *1024,
                    'socket_buffer_size' => 500 * 1024 *1024,
                    //'task_worker_num' => 20,
                    'open_length_check' => true,
                    'package_length_type' => 'N',
                    'package_length_offset' => 0,
                    'package_body_offset' => 4,
                    'package_max_length' => 200000000,
                    'heartbeat_check_interval' => 5,
    				'heartbeat_idle_time' => 60
                    //'log_file' => $task_config['logfile']
                )
            );
        } else {
            $this->server->set(
            array(
                'worker_num'  => 4,
                'daemonize' => true,
                'buffer_output_size' => 500 * 1024 *1024,
                'socket_buffer_size' => 500 * 1024 *1024,
                'open_length_check' => true,
                'package_length_type' => 'N',
                'package_length_offset' => 0,
                'package_body_offset' => 4,
                'package_max_length' => 200000000,
                'heartbeat_check_interval' => 5,
    			'heartbeat_idle_time' => 60,
                //'task_worker_num' => 20
            )
            );
        }
        
        $process = new swoole_process(function($process) use($tickobj) {
            swoole_timer_tick($tickobj->timetick,function() use($tickobj){ //定时检查是否还有未处理的sql
                    if($tickobj->countquerysql->get()>0){
                        if($tickobj->dilatationpool->get() <= $tickobj->dilatationpool_max){
                           $tickobj->releasedata($tickobj->server,1);
                           $tickobj->dilatationpool->add(1);
                        }
                    }
            });
        });

        $this->server->addProcess($process);

        $this->server->on('Receive', array($this , 'onReceive'));

        $this->server->on('Task', array($this , 'onTask'));

        $this->server->on('Finish', array($this , 'onFinish'));

        $this->server->start();
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        $param = array(
            'fd' => $fd,
            'data'=>json_decode($this->unpackmes($data), true)
        );
         if($param['data']['type']=='sql'){
            $tmpdata=array(
                'fd' =>$fd,
                'sql'=>$param['data']['data']
                 );
            try{
                $this->lock->lock();
                if($this->redisinstance->PING()=='+PONG'){
                    $this->redisinstance->LPUSH('syncsql',json_encode($tmpdata,true));
                }else{
                    $errordata=array(
                        'error' =>'RedisServer  connect() failed: Connection refused '
                    );
                    $this->msgtable->set('errno',$errordata);
                }
                $this->countquerysql->add(1);
                $this->lock->unlock();
                $this->tasksql($serv);
            }catch(Exception $e){
                $errordata=array(
                'error' =>'Server '. $e->getMessage()
                );
                $this->msgtable->set('errno',$errordata);
            }
            if($this->msgtable->get('errno','error')!=''){
                $errresult['success']=false;
                $errresult['error']=$this->msgtable->get('errno','error');
                $this->lock->unlock();
                $serv->send($fd,$this->packmes(json_encode($errresult,true)));
            }
        }

        // elseif ($param['data']['type']=='async') {
        //     $returnresult=$serv->task($data);
        // }
    }
    
    public function onTask($serv, $task_id, $from_id, $data)
    {
        $data=json_decode($this->unpackmes($data), true);
        if($data['model']=='sql'){
         $this->pool->query($data['data'], function (swoole_mysql $mysqli, $result)
            {
                   if ($result === true)
                    {
                        $return_result['success']=true;
                        $return_result['insert_id']= $mysqli->insert_id;
                        $return_result['affected_rows']= $mysqli->affected_rows;
                    }
                    elseif ($result === false)
                    {
                        $return_result['success']=false;
                        $return_result['error']= $mysqli->error;
                    }
                    else
                    {

                        $return_result['success']= true;
                        $return_result['data']=$result;
                    }
                    // print_r(json_encode($return_result,true));
                    // return $this->packmes(json_encode($return_result,true));
            });
        }

    }

    public function onFinish($serv, $task_id, $data)
    {
        // echo "Task {$task_id} finish\n";
        // echo "Result: {$data}\n";
    }

     /**
     *
     * 同步步执行sql
     */

    private function tasksql($serv){
         $poolsize=$this->busy_pool_size->get();
         if($poolsize>$this->poolsize_max){
            //自动扩容
            if($poolsize>$this->poolreease_max && $this->dilatationpool->get() <= $this->dilatationpool_max){
                $this->releasedata($serv);
                $this->dilatationpool->add(1);
            }
         }else{
            $this->releasedata($serv);
         }
         
        
    }


    /*
    *
    *扩容处理方式
     */
    private function releasedata($serv,$flag=0){
            $db = new swoole_mysql;
            $connectobj=$this;
            $redisdb = new Redis();
            $redisdb->connect($this->redisconfig['host'], $this->redisconfig['port']);
            $db->connect($this->sqlconfig,function($db,$r) use($connectobj,$redisdb,$serv){
                if ($r === false) {
                     $log = array(
                                'path' => dirname(__DIR__) .'/log/'.date('Ymd',time()).'/'.date('Ymd',time()).'.log', 
                                'content' => "时间：".date('Ymd-H:i:s',time())."\r\n错误编号为:{$db->connect_errno},错误内容为：{$db->connect_error}\r\n"
                            );
                      $connectobj->log($log);
                      if($flag==1){
                      	$connectobj->dilatationpool->sub(1);
                      }
                      $errordata=array(
                        'error' =>$log['content']
                        );
                    $connectobj->msgtable->set('errno',$errordata);
                    $db->close();
                    $redisdb->close();
                    $db=NULL;
                    $redisdb=NULL;
                }else{
                    $connectobj->busy_pool_size->add(1);
                    $connectobj->querydata($db,$redisdb,$serv);
                    };
                });
    }
    /*
    *
    *扩容处理sql数据
     */

    private function querydata($db,$redisdb,$serv){
        $obj=$this;
        if($redisdb!=NULL){
            if($redisdb->PING()=='+PONG'){
                $errordata=array(
                        'error' =>''
                    );
                $this->msgtable->set('errno',$errordata);
            }else{
            $errordata=array(
                        'error' =>'RedisServer  connect() failed: Connection refused '
                    );
                $this->msgtable->set('errno',$errordata);
                $redisdb->close();
                $redisdb = new Redis();
                $redisdb->connect($this->redisconfig['host'], $this->redisconfig['port']);
                $this->querydata($db,$redisdb,$serv);
            }
            $tmpdata=$redisdb->RPOP('syncsql');
            if(isset($tmpdata) && $tmpdata){
            	$data=json_decode($tmpdata,true);
            }
        }
        if($this->countquerysql->get()>0) {
            $this->countquerysql->sub(1);
        }
     
        if(isset($data) && $data){
            $db->query($data['sql'], function(swoole_mysql $db, $r) use($obj,$data,$redisdb,$serv){
                    if ($r === true){
                            $return_result['success']=true;
                            $return_result['insert_id']= $db->insert_id;
                            $return_result['affected_rows']= $db->affected_rows;
                        }elseif ($r === false){
                            $log = array(
                                'path' => dirname(__DIR__) .'/log/'.date('Ymd',time()).'/'.date('Ymd',time()).'.log', 
                                'content' => "时间：".date('Ymd-H:i:s',time())."\r\n错误编号为:{$db->errno},错误内容为：{$db->error}\r\n打印数据为:".json_encode($param)."\r\n"
                            );
                            $obj->log($log);
                            if($db->errno == 2006 || $db->errno == 2013 ){ //mysql断线处理
                            	$redisdb->RPUSH('syncsql',json_encode($data,true));//断线后重新插入sql，防止sql丢失
                                $db->close();
                                $redisdb->close();
                                $db=NULL;
                                $redisdb=NULL;
                                $obj->taskcount->sub(1);
                            }
                            $return_result['success']=false;
                            $return_result['error']= $db->error;
                        }else{
                            $return_result['success']= true;
                            $return_result['data']=$r;
                        }
                        if($db!=NULL && $redisdb!=NULL && $redisdb->LLEN('syncsql')==0){
                            if($obj->busy_pool_size->get()>0){
                                $obj->busy_pool_size->sub(1);
                            }
                            if($obj->dilatationpool->get()>0){
                                $obj->dilatationpool->sub(1);
                            }
                                $db->close();
                                $redisdb->close();
                                $db=NULL;
                                $redisdb=NULL;
                        } 
                         
                         $obj->taskcount->add(1);
                         
                         if($db!=NULL && $redisdb!=NULL && $obj->taskcount->get()==$obj->tasksql_max){
                            if($obj->busy_pool_size->get()>0){
                                $obj->busy_pool_size->sub(1);
                            }
						   	if($obj->dilatationpool->get()>0){
                                $obj->dilatationpool->sub(1);
                            }
                            $db->close();
                            $redisdb->close();
                            $obj->taskcount->set(0);
                        }
                        $obj->querydata($db,$redisdb,$serv);
                        $serv->send($data['fd'],$obj->packmes(json_encode($return_result,true)));
                                                        
                });
        }else{
            if($db!=NULL && $redisdb!=NULL && $this->countquerysql->get()==0){
                $db->close();
                $redisdb->close();
                $db=NULL;
                $redisdb=NULL;
            }
             if($db!=NULL && $redisdb!=NULL && $redisdb->LLEN('syncsql')>0){
                 $this->querydata($db,$redisdb,$serv);
             }
             if($db!=NULL && $redisdb!=NULL && $redisdb->LLEN('syncsql')==0){
                $db->close();
                $redisdb->close();
                $db=NULL;
                $redisdb=NULL;
             }            
        }

    }


    /*
    *
    *数据库连接池
    *
     */

    private function PoolData($sql,$serv,$fd,$param){
        $pool=$this->pool;
        $obj=$this;
        $this->pool->query($sql, function (swoole_mysql $mysqli, $result) use ($serv,$fd,$pool,$param,$obj)
            {
                   if ($result === true)
                    {
                        $return_result['success']=true;
                        $return_result['insert_id']= $mysqli->insert_id;
                        $return_result['affected_rows']= $mysqli->affected_rows;
                    }
                    elseif ($result === false)
                    {
                        $log = array(
                            'path' => dirname(__DIR__) .'/log/'.date('Ymd',time()).'/'.date('Ymd',time()).'.log', 
                            'content' => "时间：".date('Ymd-H:i:s',time())."\r\n错误编号为:{$mysqli->errno},错误内容为：{$mysqli->error}\r\n打印数据为:".json_encode($param)."\r\n"
                        );
                        $obj->log($log);
                        if($mysqli->errno == 2006 || $mysqli->errno == 2013 ){ //断线重连
                          $pool->failure();
                          $pool->remove($mysqli);
                          $obj->PoolData($sql,$serv,$fd,$param);
                        }else{
                             $return_result['success']=false;
                             $return_result['error']= $mysqli->error;
                        }
                    }
                    else
                    {

                        $return_result['success']= true;
                        $return_result['data']=$result;
                    }
                    $serv->send($fd,$this->packmes(json_encode($return_result,true)));
            });
    }

    /**
     * 名称:  请求接口获取数据
     * 参数:  string $key     接口地址
     * 返回值: array   数据;
     */

    private function GetData($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
        $output = curl_exec($ch);
        curl_close($ch);
        if (empty($output)) {
            return ;
        }
        $result = json_decode($output, true);
        return $result;
    }

    /**
     * 名称:  请求接口提交数据
     * 参数:  string $key     接口地址
     * 参数:  array $data     提交数据
       参数： bool  $json    是否json提交
     * 参数： bool  $token     token值
     * 返回值: array   数据;
     */

    private function PostData($url, $data, $json = false, $token = false)
    {
        $datastring = $json ? json_encode($data) : http_build_query($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url) ;
        curl_setopt($ch, CURLOPT_POST, 1) ;
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
        if ($json) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($datastring))
            );
        }
        if ($token) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($datastring),
                    'Authorization:'.$token
                )
            );
        }
        $output=curl_exec($ch);
        if (curl_errno($ch)) {
            print_r(curl_error($ch));
        }
        curl_close($ch) ;
        if (empty($output)) {
            return ;
        }
        $result = json_decode($output, true);
        return $result;
    }

    private function getfriend($uid){
        ob_start();
        $this->application->execute(array('swoole_model','getfriend'),$uid);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
     //包装数据
    public function packmes($data, $format = '\r\n\r\n', $preformat = '######')
    {
        //return $preformat . json_encode($data, true) . $format;
        return pack('N', strlen($data)) . $data;
    }
 
    //解包装数据
    public function unpackmes($data, $format = '\r\n\r\n', $preformat = '######')
    {
        
        $resultdata = substr($data, 4);
        return $resultdata;
    }

    private function log($data)
    {
        if (!file_put_contents( $this->exitdir($data['path']), $data['content']."\r\n", FILE_APPEND )) {
            return false;
        }
        return true;
    }

    private function exitdir($dir)
    {
        $dirarr=pathinfo($dir);
        if (!is_dir( $dirarr['dirname'] )) {
            mkdir( $dirarr['dirname'], 0777, true);
        }
        return $dir;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new MySQLPollServer;
        }
        return self::$instance;
    }
}

MySQLPollServer::getInstance();

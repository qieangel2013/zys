<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 文件名称：数据库连接池类
| 功能 :用户信息操作
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/3/25
|---------------------------------------------------------------
*/
class DbServer
{
    public static $instance;
    public $http;
    private $application;
    private $config;
    private $Serconfig;
    protected $pool_size = 20;
    protected $idle_pool = array(); 
    protected $busy_pool = array(); 
    protected $wait_queue = array(); 
    protected $wait_queue_max = 100; 
    public function __construct() {
        define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
        $this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
        $this->application->bootstrap();
        $config_obj=Yaf_Registry::get("config");
        $this->config=$config_obj->database->config->toArray();
        $this->Serconfig=$config_obj->DbServer->toArray();
        $this->http = new swoole_server("0.0.0.0", $this->Serconfig['port']);
        $this->http->set(
            array(
            'worker_num' => 1,
            //'task_worker_num' => 10,
            'max_request' => 0,
            'daemonize' => true,
            'dispatch_mode' => 1,
            'log_file' => $this->Serconfig['logfile']
            )
        );
         //$this->process = new swoole_process(function($process) use($http) {
            /*for ($i = 0; $i < $this->pool_size; $i++) {
            $db = new mysqli;
            $db->connect($this->config['host'],$this->config['user'],$this->config['pwd'],$this->config['name']);
            //设置数据库编码
            $db->query("SET NAMES '".$this->config['charset']."'");
            $db_sock = swoole_get_mysqli_sock($db);
            swoole_event_add($db_sock, array($this, 'onSQLReady'));
            $this->idle_pool[] = array(
                'mysqli' => $db,
                'db_sock' => $db_sock,
                'fd' => 0,
            );
        }*/
           // print_r($this->idle_pool);
            //print_r($this->taskid);
           //$http->sendMessage('zqf \n',0);
       //});
         //$this->process->start();
        //$http->addProcess($this->process);
        $this->http->on('WorkerStart',array(&$this , 'onStart'));
        $this->http->on('Receive',array(&$this , 'onReceive'));
        //$http->on('pipeMessage',array($this , 'onpipeMessage'));
        //$this->http->on('Task',array(&$this , 'onTask'));
        //$this->http->on('Finish',array(&$this , 'onFinish'));
        $this->http->start();
    }
     public function onStart($serv)
    {
            for ($i = 0; $i < $this->pool_size; $i++) {
            $db = new mysqli;
            $db->connect($this->config['host'],$this->config['user'],$this->config['pwd'],$this->config['name']);
            //设置数据库编码
            $db->query("SET NAMES '".$this->config['charset']."'");
            $db_sock = swoole_get_mysqli_sock($db);
            swoole_event_add($db_sock, array(&$this, 'onSQLReady'));
            $this->idle_pool[] = array(
                'mysqli' => $db,
                'db_sock' => $db_sock,
                'fd' => 0,
            );
        }        

    }
     public function onpipeMessage($serv, $src_worker_id, $data)
    {
       //echo "#{$serv->worker_id} message from #$src_worker_id: $data\n";
       
        //$this->idle_pool=json_decode($data,true);

    }
    public function onSQLReady($db_sock)
    {
        $db_res = $this->busy_pool[$db_sock];
        $mysqli = $db_res['mysqli'];
        $fd = $db_res['fd'];
        $data_select=array('status1' =>'ok','error'=>0,'errormsg'=>'','result'=>'');
        if ($result = $mysqli->reap_async_query()) {
            //$ret = var_export($result->fetch_all(MYSQLI_ASSOC), true);
            $data_select['data']=$result->fetch_all(MYSQLI_ASSOC);
            $this->http->send($fd,json_encode($data_select));
            if (is_object($result)){
                mysqli_free_result($result);
            }
        } else {
            $data_select['error']=1;
            $data_select['status1']='error';
            $data_select['errormsg']=sprintf("MySQLi Error: %s\n", mysqli_error($mysqli));
            $data_select['result']=array();
            $this->http->send($fd,json_encode($data_select));
        }
        //release mysqli object
        $this->idle_pool[] = $db_res;
        unset($this->busy_pool[$db_sock]);
        //这里可以取出一个等待请求
        if (count($this->wait_queue) > 0) {
            $idle_n = count($this->idle_pool);
            for ($i = 0; $i < $idle_n; $i++) {
                $req = array_shift($this->wait_queue);
                $this->doQuery($req['fd'], $req['sql']);
            }
        }
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        $data_rec=json_decode($data,true);
        if (count($this->idle_pool) == 0) {
            //等待队列未满
            if (count($this->wait_queue) < $this->wait_queue_max) {
                $this->wait_queue[] = array(
                    'fd' => $fd,
                    'sql' => $data_rec['sql'],
                );
                } else {
                    $this->http->send($fd, "request too many, Please try again later.");
                }
            } else {
                    $this->doQuery($fd, $data_rec);
            }
        
   
    }
    
    /*public function onTask($serv, $task_id, $from_id, $sql)
    {
            $sqls=json_decode($sql,true);
            static $link = null;
            $data_task=array('status3' =>'ok','error'=>0,'errormsg'=>'','result'=>'');
            if ($link == null) {
                $link = mysqli_connect($this->config['host'],$this->config['user'],$this->config['pwd'],$this->config['name']);
                if (!$link) {
                    $link = null;
                    $data_task['error']=1;
                    $data_task['status3']='error';
                    $data_task['errormsg']="ER:" . mysqli_error($link);
                    $data_task['result']=array();
                    $serv->send($sqls['fd'],json_encode($data_task));
                    $serv->finish("ER:" . mysqli_error($link));
                }
            }
            ///从空闲池中移除
        $db = array_pop($this->idle_pool);
        print_r($this->idle_pool);
      
            $mysqli = $db['mysqli'];
            $result = $mysqli->query($sql);
            if ($result === false) {
                if ($mysqli->errno == 2013 or $mysqli->errno == 2006) {
                    $mysqli->close();
                    $r = $mysqli->connect();
                }
            }else{
                $data_task['error']=0;
                $data_task['status3']='ok';
                $data_task['errormsg']="";
                $data_task['result']=1;
                  //print_r($data_task);
                $serv->send($sqls['fd'],json_encode($data_task));
                $serv->finish("OK:" . serialize($data_task));
            }
        $db['fd'] = $sqls['fd'];
        //加入工作池中
        $this->busy_pool[$db['db_sock']] = $db;
    }
*/
    public function doQuery($fd, $sql)
    {
        //从空闲池中移除
        $db = array_pop($this->idle_pool);
        /**
         * @var mysqli
         */
        $mysqli = $db['mysqli'];

        for ($i = 0; $i < 2; $i++) {
            if($sql['type']==1){
                $result = $mysqli->query($sql['sql']);
            }else{
                $result = $mysqli->query($sql['sql'], MYSQLI_ASYNC);
            }
            if ($result === false) {
                if ($mysqli->errno == 2013 or $mysqli->errno == 2006) {
                    $mysqli->close();
                    $r = $mysqli->connect();
                    if ($r === true) continue;
                }
            }else{
                if($sql['type']==1){
                     $data_arr=array('status' =>'ok','error'=>0,'errormsg'=>'','result'=>'');
                     $data_arr['result']=$result;
                     $this->http->send($fd,json_encode($data_arr));
                     $this->idle_pool[] = $db;
                    //unset($this->busy_pool[$db_sock]);
                    //这里可以取出一个等待请求
                    if (count($this->wait_queue) > 0) {
                        $idle_n = count($this->idle_pool);
                        for ($i = 0; $i < $idle_n; $i++) {
                            $req = array_shift($this->wait_queue);
                            $this->doQuery($req['fd'], $req['sql']);
                            }
                        }
                }
            }
            break;
        }
        if($sql['type']!=1){
            $db['fd'] = $fd;
            //加入工作池中
            $this->busy_pool[$db['db_sock']] = $db;
        }
    }
     /*public function onFinish($serv, $data)
    {
        print_r($data);
    }*/
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DbServer;
        }
        return self::$instance;
    }
}
DbServer::getInstance();
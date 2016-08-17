<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/6/25
|---------------------------------------------------------------
*/
//namespace server;
//use DistributedClient;
class DistributedServer
{
	public static $instance;
	private $application;
	public $b_server_pool=[];
	public $client_pool=[];
	public $client_a;
	private $table;
	private $localip;
	private $connectioninfo;
    private $curpath;
	public function __construct() {
		$this->table = new swoole_table(1024);
		$this->table->column('serverfd', swoole_table::TYPE_INT, 8); 
		$this->table->create();
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		define('MYPATH', dirname(APPLICATION_PATH));
		$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();
		$config_obj=Yaf_Registry::get("config");
		$distributed_config=$config_obj->distributed->toArray();
		$server = new swoole_server($distributed_config['ServerIp'],$distributed_config['port'],SWOOLE_PROCESS,SWOOLE_SOCK_TCP);
		if(isset($distributed_config['logfile'])){
			$server->set(
			array(
            'worker_num'            => 4,
            'task_worker_num' 		=> 4,
            'dispatch_mode'         => 4, //1: 轮循, 3: 争抢
            //'open_length_check'     => true, //打开包长检测
            //'package_max_length'    => 8192000, //最大的请求包长度,8M
            //'package_length_type'   => 'N', //长度的类型，参见PHP的pack函数
            //'package_length_offset' => 0,   //第N个字节是包长度的值
            //'package_body_offset'   => 0,   //从第几个字节计算长度
            'daemonize' => true,
            'log_file' => $distributed_config['logfile']
			)
			);
		}else{
			$server->set(
			array(
            'worker_num'            => 4,
            'task_worker_num' 		=> 4,
            'dispatch_mode'         => 4, //1: 轮循, 3: 争抢
            //'open_length_check'     => true, //打开包长检测
            //'package_max_length'    => 8192000, //最大的请求包长度,8M
            //'package_length_type'   => 'N', //长度的类型，参见PHP的pack函数
           // 'package_length_offset' => 0,   //第N个字节是包长度的值
            //'package_body_offset'   => 0,   //从第几个字节计算长度
            'daemonize' => true
			)
			);
		}
		
		require_once __DIR__. "/DistributedClient.php";
		$server->on('Start',array(&$this , 'onStart'));
		$server->on('WorkerStart',array(&$this , 'onWorkerStart'));
		$server->on('Connect',array(&$this , 'onConnect'));
		$server->on('Receive',array(&$this , 'onReceive'));
		$server->on('Task',array(&$this , 'onTask'));
		$server->on('Finish',array(&$this , 'onFinish'));
		$server->on('Close',array(&$this , 'onClose'));
		$server->on('ManagerStop',array(&$this , 'onManagerStop'));
		$server->on('WorkerError',array(&$this , 'onWorkerError'));
		$server->start();
	}

	public function onStart($serv){
		$localinfo=swoole_get_local_ip();
		$this->localip=$localinfo['eth0'];
		$serverlist=DistributedClient::getInstance()->getserlist();
		$result_fd=json_decode($serverlist,true);
		if(!empty($result_fd)){
			foreach($result_fd as $id=>$fd){
				if($fd!=$localinfo['eth0']){
					$client=DistributedClient::getInstance()->addServerClient($fd);
					$this->table->set(ip2long($fd),array('serverfd'=>ip2long($fd)));
					$this->b_server_pool[ip2long($fd)]=array('fd' =>$fd,'client'=>$client);
				}
    		}
		}
		DistributedClient::getInstance()->appendserlist($this->localip,ip2long($this->localip));
	}

	public function onWorkerStart($serv,$worker_id){
		//swoole_timer_tick(1000,array(&$this , 'onTimer'));
	}

	public function onConnect($serv,$fd){
		$this->connectioninfo=$serv->connection_info($fd);
		$localinfo=swoole_get_local_ip();
		$this->localip=$localinfo['eth0'];
		if($this->localip!=$this->connectioninfo['remote_ip']){
			$this->client_pool[ip2long($this->connectioninfo['remote_ip'])]= array(
            'fd' => $fd,
            'remote_ip'=>$this->connectioninfo['remote_ip']
        	);
		}
            
	}
	public function onReceive($serv, $fd, $from_id, $data) {
        $remote_info=json_decode($data, true);
        //判断是否为二进制图片流
        if(!is_array($remote_info)){
            if(is_dir(MYPATH.dirname($this->curpath['path'])) && is_readable(MYPATH.dirname($this->curpath['path']))){
            }else{
                mkdir(MYPATH.dirname($this->curpath['path']),0777,true);
            }
            file_put_contents(MYPATH.$this->curpath['path'],$data);//写入图片流
        }else{
            if($remote_info['type']=='system' && $remote_info['data']['code']==10001){
         		if($this->client_a!=$remote_info['data']['fd']){
         			if(!$this->table->get(ip2long($remote_info['data']['fd']))){
         				$client=DistributedClient::getInstance()->addServerClient($remote_info['data']['fd']);
         				$this->b_server_pool[ip2long($remote_info['data']['fd'])]=array('fd' =>$remote_info['data']['fd'],'client'=>$client);
         				$this->client_a=$remote_info['data']['fd'];
         			}else{
         				if(DistributedClient::getInstance()->getkey()){
         					$client=DistributedClient::getInstance()->addServerClient($remote_info['data']['fd']);
         					$this->b_server_pool[ip2long($remote_info['data']['fd'])]=array('fd' =>$remote_info['data']['fd'],'client'=>$client);
         					$this->client_a=$remote_info['data']['fd'];
        					if($this->localip==DistributedClient::getInstance()->getkey()){
        						DistributedClient::getInstance()->delkey();
        					}
         				}
         			}
         			
        		}
        }else{
        	   switch ($remote_info['type']) {
        		case 'sql':
        			if($this->localip==$this->connectioninfo['remote_ip']){
                        foreach ($this->b_server_pool as $k => $v) {
                            $v['client']->send($data);
                        }
        				$serv->send($fd,$serv->taskwait($remote_info['data']));
        			}else{
        				print_r($remote_info);
                        $serv->task($remote_info['data']);
        			}
        			break;
        		case 'file':
        			if($this->localip==$this->connectioninfo['remote_ip']){
                        foreach ($this->b_server_pool as $k => $v) {
                           $v['client']->send($data);
                           $v['client']->sendfile(MYPATH.$remote_info['data']['path']);
                        }
                        $serv->send($fd,$serv->taskwait($remote_info['data']));
                    }else{
                        if(isset($remote_info['data']['path'])){
                            $this->curpath=$remote_info['data'];
                        }
                        $serv->task($remote_info['data']);
                    }
        			break;
        		default:
        			break;
        	   }
            }
        
        }
        print_r($remote_info);
	}
	 /**
     * 服务器断开连接
     * @param $cli
     */
    public function onClose($server,$fd,$from_id)
    {
    	if(!empty($this->client_pool)){
    		foreach ($this->client_pool as $k => $v) {
        		if($v['fd']==$fd){
        			DistributedClient::getInstance()->removeuser($v['remote_ip'],'Distributed');
        			print_r($v['remote_ip']." have closed\n");
        			unset($this->client_pool[$k]);
        		}
        	}
    	}else{
    		DistributedClient::getInstance()->removeuser($this->localip,'Distributed');
        	print_r($this->localip." have closed\n");
    	}
        
    }

    public function onManagerStop($serv){
    	if(empty($this->client_pool)){
    		DistributedClient::getInstance()->removeuser($this->localip,'Distributed');
        	print_r($this->localip." have closed\n");
    	}
    }

    public function onWorkerError($serv, $worker_id, $worker_pid, $exit_code){
    	if(empty($this->client_pool)){
    		DistributedClient::getInstance()->removeuser($this->localip,'Distributed');
        	print_r($this->localip." have closed\n");
    	}
    }

	public function onTask($serv, $task_id, $from_id, $data) {
        /* ob_start();
        $this->application->execute(array('swoole_taskclient','query'),$data);
        $result = ob_get_contents();
        ob_end_clean();*/
        $result=json_encode(array('mes' =>12));
        return $result;
	}
	public function onFinish($serv, $task_id, $data) {
		
	}
	public function onTimer($timer_id,$params = null) {
		$serverlist=DistributedClient::getInstance()->geterrlist(json_encode($this->b_server_pool));
		if($serverlist){
			unset($this->b_server_pool[ip2long($serverlist)]);
			$this->table->del(ip2long($serverlist));
		}
	}
	public static function getInstance() {
		if (!(self::$instance instanceof DistributedServer)) {
            self::$instance = new DistributedServer;
        }
        return self::$instance;
	}
}

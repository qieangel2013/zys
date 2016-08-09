<?php
//namespace server;
//use DistributedClient;
class DistributedServer
{
	public static $instance;
	private $application;
	public $server_pool=[];
	public $b_server_pool=[];
	public $client_pool=[];
	public $client_a;
	private $table;
	public function __construct() {
		$this->table = new swoole_table(1024);
		$this->table->column('serverfd', swoole_table::TYPE_INT, 8); 
		$this->table->create();
		$server = new swoole_server("0.0.0.0", 9504,SWOOLE_PROCESS,SWOOLE_SOCK_TCP);
		$server->set(
			array(
            'worker_num'            => 1,
            'dispatch_mode'         => 4, //1: 轮循, 3: 争抢
            //'open_length_check'     => true, //打开包长检测
            //'package_max_length'    => 8192000, //最大的请求包长度,8M
            //'package_length_type'   => 'N', //长度的类型，参见PHP的pack函数
            //'package_length_offset' => 0,   //第N个字节是包长度的值
            //'package_body_offset'   => 0,   //从第几个字节计算长度
            'daemonize' => true
			)
		);
		require_once __DIR__. "/DistributedClient.php";
		$server->on('Start',array(&$this , 'onStart'));
		$server->on('Connect',array(&$this , 'onConnect'));
		$server->on('Receive',array(&$this , 'onReceive'));
		$server->on('Task',array(&$this , 'onTask'));
		$server->on('Finish',array(&$this , 'onFinish'));
		$server->on('Close',array(&$this , 'onClose'));
		$server->start();
	}

	public function onStart($serv){
		$localinfo=swoole_get_local_ip();
		$serverlist=DistributedClient::getInstance()->getserlist();
		$result_fd=json_decode($serverlist,true);
		if(!empty($result_fd)){
			foreach($result_fd as $id=>$fd){
				if($fd!=$localinfo['eth0']){
					array_unshift($this->server_pool,ip2long($fd));
					$this->table->set(ip2long($fd),array('fd'=>ip2long($fd)));
					DistributedClient::getInstance()->addServerClient($fd);
				}
    		}
		}
		DistributedClient::getInstance()->appendserlist($localinfo['eth0']);
		array_unshift($this->server_pool,ip2long($localinfo['eth0']));
	}

	public function onConnect($serv,$fd){
		$connectioninfo=$serv->connection_info($fd);
        $this->client_pool[$fd]= array(
            'fd' => $fd,
            'remote_ip'=>$connectioninfo['remote_ip']
        );    
	}
	public function onReceive($serv, $fd, $from_id, $data) {
		$remote_info=json_decode($data, true);
        if($remote_info['code']==10001){
         		if($this->client_a!=$remote_info['fd']){
         			if(!$this->table->get(ip2long($remote_info['fd']))){
         				$this->client_a=$remote_info['fd'];
         				DistributedClient::getInstance()->addServerClient($remote_info['fd']);
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
        foreach ($this->client_pool as $k => $v) {
        	if($k==$fd){
        		DistributedClient::getInstance()->removeuser($v['remote_ip'],'Distributed');
        		print_r($v['remote_ip']." have closed\n");
        	}
        }
    }
	public function onTask($serv, $task_id, $from_id, $data) {
        $fd = json_decode($data, true);
        $tmp_data=$fd['data'];
        $this->application->execute(array('swoole_task','demcode'),$tmp_data);
        $serv->send($fd['fd'] , "Data in Task {$task_id}");
        return  'ok';
	}
	public function onFinish($serv, $task_id, $data) {
		echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
	}
	public static function getInstance() {
		if (!(self::$instance instanceof DistributedServer)) {
            self::$instance = new DistributedServer;
        }
        return self::$instance;
	}
}

//DistributedServer::getInstance();

<?php
//namespace server;
class DistributedClient
{
	public $application;
    public static $instance;
    public $c_client_pool=[];
    public $b_client_pool=[];
    private $table;
	public function __construct() {
        $this->table = new swoole_table(1024);
        $this->table->column('clientfd', swoole_table::TYPE_INT, 8); 
        $this->table->create();
	}

	 public function addServerClient($address)
    {
       	$client = new swoole_client(SWOOLE_TCP, SWOOLE_SOCK_ASYNC);
        $client->on('Connect', array(&$this, 'onConnect'));
        $client->on('Receive', array(&$this, 'onReceive'));
        $client->on('Close', array(&$this, 'onClose'));
        $client->on('Error', array(&$this, 'onError'));
        $config_obj=Yaf_Registry::get("config");
        $distributed_config=$config_obj->distributed->toArray();
        $client->connect($address,$distributed_config['port']);
        $this->table->set(ip2long($address),array('clientfd'=>ip2long($address)));
        $this->b_client_pool[ip2long($address)] = $client;
    }

    public function onConnect($serv) {
        $localinfo=swoole_get_local_ip();
        $serv->send(json_encode(array('code' =>10001,'status'=>1,'fd'=>$localinfo['eth0'])));
    }

	public function onReceive($serv, $fd, $from_id, $data) {
		/*$remote_info=json_decode($data, true);
        if($remote_info['code']==10002){
            $this->c_client_pool[ip2long($remote_info['fd'])]= array('fd' =>$fd,'client'=>$client);

        }*/
        //$remote_info=json_decode($data, true)
        // start a task
        //$serv->task(json_encode($param));
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
	 /**
     * 服务器断开连接
     * @param $cli
     */
    public function onClose($client)
    {
        //print_r("close\n");
        unset($client);
    }
    /**
     * 服务器连接失败
     * @param $cli
     */
    public function onError($client)
    {
        //print_r("error\n");
        unset($client);
    }
    //获取分布式服务器列表
    public function getserlist($keyname='Distributed'){
        ob_start();
        distributed_dredis::getInstance()->getfd($keyname);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
    //添加到分布式服务器列表
    public function appendserlist($data,$keyname='Distributed'){
        distributed_dredis::getInstance()->savefd($data,$keyname);
    }
    //从分布式服务器列表删除
    public function removeuser($data,$keyname='Distributed'){
        distributed_dredis::getInstance()->removefd($data,$keyname);
    }
    //单例
    public static function getInstance() {
        if (!(self::$instance instanceof DistributedClient)) {
            self::$instance = new DistributedClient;
        }
        return self::$instance;
    }
}


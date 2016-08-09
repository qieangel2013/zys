<?php
class SwooleLiveServer
{
	public static $instance;
	private $table;
	public function __construct() {

		$this->table = new swoole_table(1024);
		$this->table->column('id', swoole_table::TYPE_INT, 8);       //1,2,4,8
		$this->table->create();
		$config_obj=Yaf_Registry::get("config");
		$live_config=$config_obj->live->toArray();
		$server = new swoole_websocket_server($live_config['ServerIp'], $live_config['port']);
		if(isset($live_config['logfile'])){
			$server->set(
			array(
				'daemonize' => true,
				'log_file' => $live_config['logfile']
			)
			);
		}else{
			$server->set(
			array(
				'daemonize' => true
			)
			);
		}
		
		$server->on('Open',array($this , 'onOpen'));
		$server->on('Message',array($this , 'onMessage'));
		$server->on('Close',array($this , 'onClose'));
		$server->start();
	}
	public function onOpen($server, $req) {
		$this->table->set($req->fd, array('id'=>$req->fd));

	}
	public function onMessage($server, $frame) {
		$framedata=json_decode($frame->data,true);
		if('smes_closed'==$framedata['data']){
			$server->Close($frame->fd);
		}else{


			foreach($this->table as $row)
			{

				if($framedata['type']=='mess'){
					$data_mes['data']='游客'.$frame->fd.'说：' .$framedata['data'];
					$data_mes['type']=$framedata['type'];
					$server->push($row['id'],json_encode($data_mes,true));
				}elseif($framedata['type']=='video'){
					$server->push($row['id'],$frame->data);
				}elseif($framedata['type']=='mic'){
					$server->push($row['id'],$frame->data);
				}
			}

		}
	}
	public function onClose($server, $fd) {
	$this->table->del($fd);
	}
	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new SwooleLiveServer;
		}
		return self::$instance;
	}
}
SwooleLiveServer::getInstance();

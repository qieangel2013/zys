<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
class SwooleLiveServer
{
	public static $instance;
	private $application;
	public function __construct() {
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		define('MYPATH', dirname(APPLICATION_PATH));
		$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();
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
		//echo $req->fd;
		$this->application->execute(array('swoole_socket','savefd'),$req->fd);
	}
	public function onMessage($server, $frame) {
		ob_start();
		$this->application->execute(array('swoole_socket','getfd'));
		$result = ob_get_contents();
		ob_end_clean();
		/*for($i=1 ; $i<= $result ; $i++) {
        	$server->push($i,'游客'.$frame->data);
    	}*/
    	$framedata=json_decode($frame->data,true);
    	if('smes_closed'==$framedata['data']){
    		$server->Close($frame->fd);
    	}else{
			$result_fd=json_decode($result,true);
		    foreach($result_fd as $id=>$fd){
		    	if($framedata['type']=='mess'){
		    		$data_mes['data']='游客'.$frame->fd.'说：' .$framedata['data'];
		    		$data_mes['type']=$framedata['type'];
		    		$server->push($fd,json_encode($data_mes,true));
		    	}elseif($framedata['type']=='video'){
		    		$server->push($fd,$frame->data);
		    	}elseif($framedata['type']=='mic'){
                                $server->push($fd,$frame->data);
                        }

        		
    	    }
    	}
	}
	public function onClose($server, $fd) {
		$this->application->execute(array('swoole_socket','removefd'),$fd);
	}
	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new SwooleLiveServer;
        }
        return self::$instance;
	}
}

SwooleLiveServer::getInstance();

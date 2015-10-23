<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
class WebSocketServer
{
	public static $instance;
	private $application;
	public function __construct() {
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();

		$server = new swoole_websocket_server("0.0.0.0", 9503);

		$server->set(
			array(
				'daemonize' => true
			)
		);

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
        	$server->push($i,'游客'.$frame->fd.'说：' .$frame->data);
    	}*/
    	if('smes_closed'==$frame->data){
    		$server->Close($frame->fd);
    	}else{
			$result_fd=json_decode($result,true);
		    foreach($result_fd as $id=>$fd){
        		$server->push($fd,'游客'.$frame->fd.'说：' .$frame->data);
    	    }
    	}
	}
	public function onClose($server, $fd) {
		$this->application->execute(array('swoole_socket','removefd'),$fd);
	}
	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new WebSocketServer;
        }
        return self::$instance;
	}
}

WebSocketServer::getInstance();

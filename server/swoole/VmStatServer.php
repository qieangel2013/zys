<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
class VmStatServer
{
	public static $instance;
	private $application;
	private $vmstat_handle;
	public function __construct() {
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();

		$server = new swoole_websocket_server("0.0.0.0", 9502);

		$server->set(
			array(
				'daemonize' => true,
				'log_file' => '/server/log/vmstat.log'
			)
		);

		$server->on('Open',array($this , 'onOpen'));

		$server->on('Message',array($this , 'onMessage'));

		$server->on('Close',array($this , 'onClose'));

		$server->start();
	}

	public function onOpen($server, $req) {
		$this->application->execute(array('swoole_socket','savefd'),$req->fd);
		$this->vmstat_handle=popen('vmstat 1', 'r');

	}
	public function onMessage($server, $frame) {
		$framedata=json_decode($frame->data,true);
    	if('mess'==$framedata['type']){
    		$server->push($frame->fd,"procs -----------memory---------- ---swap-- -----io---- -system-- ----cpu----\n");
    		$server->push($frame->fd,"r  b   swpd   free   buff  cache   si   so    bi    bo   in   cs us sy id wa\n");
			ob_start();
			$this->application->execute(array('swoole_socket','getfd'));
			$result = ob_get_contents();
			ob_end_clean();
			$result_fd=json_decode($result,true);
			while(!feof($this->vmstat_handle)) 
    		{ 
		    	foreach($result_fd as $id=>$fd){
        			$server->push($fd,fread($this->vmstat_handle, 1024));
    	    	}
    		} 
    	}else{
    		$server->Close($frame->fd);
    	}
		/*ob_start();
		$this->application->execute(array('swoole_socket','getfd'));
		$result = ob_get_contents();
		ob_end_clean();
			$result_fd=json_decode($result,true);
			$line = fread($this->vmstat_handle,512); 
			print_r($line);
		    foreach($result_fd as $id=>$fd){
        		$server->push($fd,$line);
    	    }*/
    	
	}
	public function onClose($server, $fd) {
		@shell_exec('killall vmstat');
    	@pclose($this->vmstat_handle);
		$this->application->execute(array('swoole_socket','removefd'),$fd);
	}
	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new VmStatServer;
        }
        return self::$instance;
	}
}

VmStatServer::getInstance();

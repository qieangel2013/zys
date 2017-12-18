<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/7/25
|---------------------------------------------------------------
*/
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
class SwooleLiveServer
{
	public static $instance;
	private $application;
	public function __construct() {
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		define('MYPATH', dirname(APPLICATION_PATH));
		$this->application = new \Yaf\Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();
		$config_obj=Yaf_Registry::get("config");
		$live_config=$config_obj->live->toArray();
		$server = new swoole_websocket_server($live_config['ServerIp'], $live_config['port']);
		//ssl配置，注意编译swoole的时候需要加上--enable-openssl选项
		//$server = new swoole_websocket_server($live_config['ServerIp'], $live_config['port'], SWOOLE_BASE, SWOOLE_SOCK_TCP | SWOOLE_SSL);
		if(isset($live_config['logfile'])){
			$server->set(
			array(
				'daemonize' => true,
				//'ssl_cert_file' => '/usr/local/nginx/ssl/xcx.tianlian.cn.crt', ssl证书
                //'ssl_key_file' => '/usr/local/nginx/ssl/xcx.tianlian.cn.key',	 ssl的key
				'log_file' => $live_config['logfile']
			)
			);
		}else{
			$server->set(
			array(
				'daemonize' => true
				//'ssl_cert_file' => '/usr/local/nginx/ssl/xcx.tianlian.cn.crt',
                //'ssl_key_file' => '/usr/local/nginx/ssl/xcx.tianlian.cn.key'
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
		    	if($fd==$frame->fd){
		    		continue;
		    	}
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

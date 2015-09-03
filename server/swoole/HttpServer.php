<?php
class HttpServer
{
	public static $instance;
	public $http;
	private $application;
	public function __construct() {
		$http = new swoole_http_server("0.0.0.0", 9501);
		$http->set(
			array(
			'worker_num' => 16,
			'daemonize' => true,
	            	'max_request' => 10000,
	            	'dispatch_mode' => 1
			)
		);
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();
		$http->on('Request',array($this , 'onRequest'));
		$http->start();
	}
	public function onRequest($request,$response) {
		$response->status('200');
		$ser=$request->server;
		$hea= $request->header;
		$hea['host']=str_replace(':9501','',$hea['host']);//如果端口号是80，就不用要此句代码
			ob_start();
			try {
				$yaf_request = new Yaf_Request_Http($ser['request_uri']);
				$yaf_request->setBaseUri($hea['host']);
			    	$this->application->getDispatcher()->dispatch($yaf_request);
			} catch ( Yaf_Exception $e ) {
				//var_dump( $e );
			}
			$result = ob_get_contents();
			ob_end_clean();
			$response->end($result);
	}
	
	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new HttpServer;
        }
        return self::$instance;
	}
}
HttpServer::getInstance();

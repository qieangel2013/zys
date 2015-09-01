<?php

class HttpServer
{
	public static $instance;

	public $http;
	public static $get;
	public static $post;
	public static $header;
	public static $server;
	private $application;

	public function __construct() {
		$http = new swoole_http_server("127.0.0.1", 9501);

		$http->set(
			array(
				'worker_num' => 16,
				'daemonize' => true,
	            'max_request' => 10000,
	            'dispatch_mode' => 1
			)
		);
		$http->on('request',function($request,$response){
				$response->status('200');
		 		$response->write(json_encode($request));
		 		//$response->write('123');
		 		//$response->end('zqf');
		});

		$http->start();
	}

	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new HttpServer;
        }
        return self::$instance;
	}
}

HttpServer::getInstance();

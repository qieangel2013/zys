<?php

class WebSocketServer
{
	public static $instance;

	public function __construct() {
		$serv = new swoole_websocket_server("127.0.0.1", 9503);
		$serv->set(
			array(
				'daemonize' => true,
			)
		);
		$serv->on('Open', function($server, $req) {
    		echo "connection open: ".$req->fd;
		});

		$serv->on('Message', function($server, $frame) {
    		$server->push($frame->fd, json_encode($frame->data));
		});

		$serv->on('Close', function($server, $fd) {
    		echo "connection close: ".$fd;
		});

		$serv->start();
	}

	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new WebSocketServer;
        }
        return self::$instance;
	}
}

WebSocketServer::getInstance();

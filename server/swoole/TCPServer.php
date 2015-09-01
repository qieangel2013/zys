<?php

class TCPServer
{
	public static $instance;

	public function __construct() {
		$serv = new swoole_server("127.0.0.1", 9502);

		$serv->set(
			array(
				'worker_num' => 16,
				'daemonize' => true,
			)
		);
		$serv->on('connect', function ($serv, $fd){
    		echo "Client:Connect.\n";
		});
		$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    			$serv->send($fd, $data);
    			$serv->close($fd);
		});
		$serv->on('close', function ($serv, $fd) {
    			echo "Client: Close.\n";
		});
		$serv->start();
	}

	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new TCPServer;
        }
        return self::$instance;
	}
}

TCPServer::getInstance();

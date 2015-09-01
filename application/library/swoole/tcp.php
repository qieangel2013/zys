<?php

class swoole_tcp
{
	public static $client;
	public function __construct() {
		self::$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
		//设置事件回调函数
		self::$client->on("connect", function($cli) {
    		$cli->send("hello world\n");
    		//echo "Connection open\n";
		});
		self::$client->on("receive", function($cli, $data){
    		echo "Received: ".$data."\n";
		});
		self::$client->on("error", function($cli){
    		echo "Connect failed\n";
		});
		self::$client->on("close", function($cli){
    			echo "Connection close\n";
		});
		self::$client->connect('127.0.0.1', 9503, 0.5,0);
	}
}
?>
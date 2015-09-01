<?php

class swoole_async
{
	public static $client;
	public function __construct() {
		$fp = stream_socket_client("tcp://127.0.0.1:9504", $code, $msg, 3);
		$http_request = "GET /index.html HTTP/1.1\r\n\r\n";
		fwrite($fp, $http_request);
		swoole_event_add($fp, function($fp){
    	echo fread($fp, 8192);
    	swoole_event_del($fp);
    	fclose($fp);
	});
	swoole_timer_after(2000, function() {
    	echo "2000ms timeout\n";
	});
	swoole_timer_tick(1000, function() {
    	echo "1000ms interval\n";
	});
	}
}
?>
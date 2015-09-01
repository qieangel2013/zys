<?php

class TaskServer
{
	public static $instance;

	public function __construct() {
		$serv = new swoole_server("127.0.0.1", 9504);
		$serv->set(array( 
    		'worker_num' => 1, 
    		'task_worker_num' => 8, //database connection pool 
    		'db_uri' => 'mysql:host=127.0.0.1;dbname=test', 
    		'db_user' => 'root', 
    		'db_passwd' => 'root', 
    		'daemonize' => true,
			)); 

		$serv->on('Receive', function($serv, $fd, $from_id, $data) {
    		$result = $serv->taskwait($data); 
    		if ($result !== false) { 
        		list($status, $db_res) = explode(':', $result, 2);  
        	if ($status == 'OK') { 
            	$serv->send($fd, var_export(unserialize($db_res), true) . "\n"); 
        	} else { 
            	$serv->send($fd, $db_res); 
        	}    
   			} else { 
        			$serv->send($fd, "Error. Task timeout\n"); 
    		} 

		});
		$serv->on('Task', function($serv, $task_id, $from_id, $sql){
    			static $link = null; 
    			if ($link == null) 
    			{ 
        		$link = new PDO($serv->setting['db_uri'], $serv->setting['db_user'], $serv->setting['db_passwd']); 
        		if (!$link) 
        		{ 
           		 $link = null; 
           		 $serv->finish("ER: connect database failed."); 
        		} 
    		} 
    			$result = $link->query($sql); 
    			if (!$result) 
    		{ 
        		$serv->finish("ER: query error");  
    		} 
    			$data = $result->fetchAll(); 
    			$serv->finish(serialize($data));
		});
		$serv->on('Finish', function($serv, $data) {
    			echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
		});
		$serv->start();
	}

	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new TaskServer;
        }
        return self::$instance;
	}
}

TaskServer::getInstance();

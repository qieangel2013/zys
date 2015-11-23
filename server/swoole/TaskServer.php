<?php
class TaskServer
{
	public static $instance;
	private $application;
	public function __construct() {
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();

		$server = new swoole_server("0.0.0.0", 9503);

		$server->set(
			array(
				'worker_num'  => 8,
				'daemonize' => true,
				'task_worker_num' => 8
			)
		);

		$server->on('Receive',array($this , 'onReceive'));

		$server->on('Task',array($this , 'onTask'));

		$server->on('Finish',array($this , 'onFinish'));

		$server->start();
	}

	public function onReceive($serv, $fd, $from_id, $data) {
		$param = array(
            'fd' => $fd,
            'data'=>json_decode($data, true)
        );
        // start a task
        $serv->task(json_encode($param));
	}
	public function onTask($serv, $task_id, $from_id, $data) {
        $fd = json_decode($data, true);
        $tmp_data=$fd['data'];
        $this->application->execute(array('swoole_task','demcode'),$tmp_data);
        $serv->send($fd['fd'] , "Data in Task {$task_id}");
        return  'ok';
	}
	public function onFinish($serv, $task_id, $data) {
		echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
	}
	public static function getInstance() {
		if (!self::$instance) {
            self::$instance = new TaskServer;
        }
        return self::$instance;
	}
}

TaskServer::getInstance();

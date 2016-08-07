<?php
namespace \server\DistributedClient ;
class DistributedClient
{
	private $application;
	public function __construct() {
		define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
		$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
		$this->application->bootstrap();
	}

	 private function addServerClient($address)
    {
       	$client = new swoole_client(SWOOLE_SOCK_TCP);
        $client->on('Connect', array($this, 'onConnect'));
        $client->on('Receive', array($this, 'onReceive'));
        $this->client->on('Close', array($this, 'onClose'));
        $this->client->on('Error', array($this, 'onError'));
        $client->connect($address,9504);
    }

    public function onConnect($data) {
        $this->send($data);
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
	
}


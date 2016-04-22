<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
class DbpoolServer
{
    public static $instance;
    public $http;
    private $application;
    public function __construct() {
        $this->http = new swoole_http_server("0.0.0.0", 9501);
        $this->http->set(
            array(
            'worker_num' => 100,
            'task_worker_num' => 20, 
            'db_uri' => 'mysql:host=127.0.0.1;dbname=youxiu',
            'db_user' => 'root',
            'db_passwd' => '123456',
            'daemonize' => true,
            'dispatch_mode' => 1,
            'log_file' => '/usr/local/nginx/html/youxiu/server/log/DbpoolServer.log'
            )
        );
        define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
        $this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
        $this->application->bootstrap();
        $this->http->on('Request',array($this , 'onRequest'));
        $this->http->on('Task',array($this , 'onTask'));
        $this->http->on('Finish',array($this , 'onFinish'));
        $this->http->start();
    }

    public function onRequest($req, $resp)
    {
        $result = $this->http->taskwait("show tables");
        if ($result !== false)
        {
            $resp->end(var_export($result['data'], true));
            return;
        }
        else
        {
            $resp->status(500);
            $resp->end("Server Error, Timeout\n");
        }
    }
    public function onTask($serv, $task_id, $from_id, $sql)
    {
        static $link = null;
        if ($link == null)
        {
            $link = new PDO($serv->setting['db_uri'], $serv->setting['db_user'], $serv->setting['db_passwd']);;
            if (!$link)
            {
                $link = null;
                return array("data" => '', 'error' => "connect database failed.");
            }
        }
        $result = $link->query($sql);
        if (!$result)
        {
            return array("data" => '', 'error' => "query error");
        }
        $data = $result->fetchAll();
        return array("data" => $data);
    }

    public function onFinish($serv, $data)
    {
        echo "AsyncTask Finish:Connect.PID=" . posix_getpid() . PHP_EOL;
    }
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DbpoolServer;
        }
        return self::$instance;
    }
}
DbpoolServer::getInstance();
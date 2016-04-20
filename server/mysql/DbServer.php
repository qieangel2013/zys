<?php
class DbServer
{
    public static $instance;
    public $http;
    private $application;
    private $config;
    private $Serconfig;
    protected $pool_size = 20;
    protected $idle_pool = array(); 
    protected $busy_pool = array(); 
    protected $wait_queue = array(); 
    protected $wait_queue_max = 100; 
    public function __construct() {
        define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
        $this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
        $this->application->bootstrap();
        $config_obj=Yaf_Registry::get("config");
        $this->config=$config_obj->database->config->toArray();
        $this->Serconfig=$config_obj->DbServer->toArray();
        $http = new swoole_server("0.0.0.0", $this->Serconfig['port']);
        $http->set(
            array(
            'worker_num' => 1,
            'max_request' => 0,
            'daemonize' => true,
            'dispatch_mode' => 1,
            'log_file' => $this->Serconfig['logfile']
            )
        );
        $http->on('WorkerStart',array($this , 'onStart'));
        $http->on('Receive',array($this , 'onReceive'));
        $http->start();
    }

   public function onStart($serv)
    {
        $this->http = $serv;

        for ($i = 0; $i < $this->pool_size; $i++) {
            $db = new mysqli;
            $db->connect($this->config['host'],$this->config['user'],$this->config['pwd'],$this->config['name']);
            //设置数据库编码
            $db->query("SET NAMES '".$this->config['charset']."'");
            $db_sock = swoole_get_mysqli_sock($db);
            swoole_event_add($db_sock, array($this, 'onSQLReady'));
            $this->idle_pool[] = array(
                'mysqli' => $db,
                'db_sock' => $db_sock,
                'fd' => 0,
            );
        }
    }
    public function onSQLReady($db_sock)
    {
        $db_res = $this->busy_pool[$db_sock];
        $mysqli = $db_res['mysqli'];
        $fd = $db_res['fd'];
        if ($result = $mysqli->reap_async_query()) {
            //$ret = var_export($result->fetch_all(MYSQLI_ASSOC), true);
            $ret = json_encode($result->fetch_all(MYSQLI_ASSOC));
            $this->http->send($fd, $ret);
            if (is_object($result)){
                mysqli_free_result($result);
            }
        } else {
            $this->http->send($fd, sprintf("MySQLi Error: %s\n", mysqli_error($mysqli)));
        }
        //release mysqli object
        $this->idle_pool[] = $db_res;
        unset($this->busy_pool[$db_sock]);
        //这里可以取出一个等待请求
        if (count($this->wait_queue) > 0) {
            $idle_n = count($this->idle_pool);
            for ($i = 0; $i < $idle_n; $i++) {
                $req = array_shift($this->wait_queue);
                $this->doQuery($req['fd'], $req['sql']);
            }
        }
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
    //echo "Received: $data\n";
        //没有空闲的数据库连接
        
    if (count($this->idle_pool) == 0) {
            //等待队列未满
            if (count($this->wait_queue) < $this->wait_queue_max) {
                $this->wait_queue[] = array(
                    'fd' => $fd,
                    'sql' => $data,
                );
            } else {
                $this->http->send($fd, "request too many, Please try again later.");
            }
        } else {
            $this->doQuery($fd, $data);
        }
    }
    
    public function doQuery($fd, $sql)
    {
        //从空闲池中移除
        $db = array_pop($this->idle_pool);
        /**
         * @var mysqli
         */
        $mysqli = $db['mysqli'];

        for ($i = 0; $i < 2; $i++) {
            $result = $mysqli->query($sql, MYSQLI_ASYNC);
            if ($result === false) {
                if ($mysqli->errno == 2013 or $mysqli->errno == 2006) {
                    $mysqli->close();
                    $r = $mysqli->connect();
                    if ($r === true) continue;
                }
            }
            break;
        }

        $db['fd'] = $fd;
        //加入工作池中
        $this->busy_pool[$db['db_sock']] = $db;
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DbServer;
        }
        return self::$instance;
    }
}
DbServer::getInstance();
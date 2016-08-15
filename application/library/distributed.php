<?php
class distributed
{
    private $client;
    public static $instance;
    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
        $config_obj=Yaf_Registry::get("config");
        $distributed_config=$config_obj->distributed->toArray();
        $localinfo=swoole_get_local_ip();
          if (!$this->client->connect($localinfo['eth0'],$distributed_config['port'], -1))
        {
            exit("connect failed. Error: {$client->errCode}\n");
        }
    }
    //sql执行
    public function query($sql) {
        $this->client->send(json_encode($sql));
        {
            $dbclient_data=json_decode($this->client->recv(),true);
        }
        return $dbclient_data;

    }
    //文件执行
    public function queryfile($data) {
        $this->client->send(json_encode($data));
        {
            $dbclient_data=json_decode($this->client->recv(),true);
        }
        return $dbclient_data;

    }

    public function close() {
        $this->client->close();
    }
    public static function getInstance() {
        if (!(self::$instance instanceof distributed)) {
            self::$instance = new distributed;
        }
        return self::$instance;
    }
}

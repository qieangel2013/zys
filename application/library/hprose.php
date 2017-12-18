<?php
require_once dirname(dirname(__DIR__)) . '/hprose/lib/Client.php';
class hprose
{
    public static $instance;
    public function __construct() {
    }
    public static function getdata()
    {
    	$hprose_config = \Yaf\Registry::get("config")->hprose->toArray();
        $client = new Client("tcp://" . $hprose_config['ServerIp'] . ":" . $hprose_config['port'],false);
        return $client->zys("zys");
    }
    public static function getInstance() {
        if (!(self::$instance instanceof hprose)) {
            self::$instance = new hprose;
        }
        return self::$instance;
    }
}


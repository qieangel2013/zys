<?php
class swoole_taskclient extends Model {
    public static $instance;
    public function __construct($table='') {
       parent::__construct($table);
    }
    public function query($sql){
        $result=self::getInstance()->query($sql);
        echo json_encode($result);
    }
    public static function getInstance() {
        if (!(self::$instance instanceof swoole_taskclient)) {
            self::$instance = new swoole_taskclient;
        }
        return self::$instance;
    }
}

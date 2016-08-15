<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/6/25
|---------------------------------------------------------------
*/
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

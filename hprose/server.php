<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/5/25
|---------------------------------------------------------------
*/
require_once dirname(__DIR__) . '/hprose/lib/Server.php';
$process = new swoole_process('hproseserver_call', true);
$pid     = $process->start();
function zys($name)
{
    return "$name is high performance service framework based on yaf and swoole\r\n";
}
function hproseserver_call(swoole_process $worker)
{
    define('APPLICATION_PATH', dirname(__DIR__) . "/application");
    define('MYPATH', dirname(APPLICATION_PATH));
    $application = new Yaf_Application(dirname(APPLICATION_PATH) . "/conf/application.ini");
    $application->bootstrap();
    $config_obj    = Yaf_Registry::get("config");
    $hprose_config = $config_obj->hprose->toArray();
    $server        = new Server("tcp://" . $hprose_config['ServerIp'] . ":" . $hprose_config['port']);
    $server->setErrorTypes(E_ALL);
    $server->setDebugEnabled();
    $server->addFunction('zys');
    $server->start();
}
swoole_process::daemon(true);
swoole_process::wait();
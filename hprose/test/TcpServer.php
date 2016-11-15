<?php
require_once dirname(__DIR__) . '/lib/Server.php';

function zys($name) {
    return "$name is high performance service framework based on yaf and swoole\r\n";
}
$server = new Server("tcp://0.0.0.0:1314");
$server->setErrorTypes(E_ALL);
$server->setDebugEnabled();
$server->addFunction('zys');
$server->start();

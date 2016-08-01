<?php
require_once __DIR__ . "/Thrift/ClassLoader/ThriftClassLoader.php";
$loader = new Thrift\ClassLoader\ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__);
$loader->registerNamespace('swoole', __DIR__);
$loader->registerNamespace('Bin', __DIR__);
$loader->registerDefinition('Bin',  __DIR__);
$loader->register();
define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
$this->application->bootstrap();
$config_obj=Yaf_Registry::get("config");
$rpc_config=$config_obj->rpc->toArray();
$socket = new Thrift\Transport\TSocket($rpc_config['host'],$rpc_config['port']);
$transport = new Thrift\Transport\TFramedTransport($socket);
$protocol = new Thrift\Protocol\TBinaryProtocol($transport);
$transport->open();

$client = new Bin\rpc\rpcClient($protocol);
$message = new Bin\rpc\Message(array('name' => 'userinfo','result'=>'{"id":37936,"name"=>"zqf",email:"904208360@qq.comn"}'));
$ret = $client->sendMessage($message);
var_dump($ret);

$transport->close();


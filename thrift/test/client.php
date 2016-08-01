<?php
define('APPLICATION_PATH_DIR', dirname(dirname(__DIR__)). "/application");
define('THRIFT_DIR_PATH',dirname(APPLICATION_PATH_DIR)."/thrift");
require_once THRIFT_DIR_PATH. "/Thrift/ClassLoader/ThriftClassLoader.php";
$loader = new Thrift\ClassLoader\ThriftClassLoader();
$loader->registerNamespace('Thrift', THRIFT_DIR_PATH);
$loader->registerNamespace('swoole', THRIFT_DIR_PATH);
$loader->registerNamespace('Bin', THRIFT_DIR_PATH);
$loader->registerDefinition('Bin',  THRIFT_DIR_PATH);
$loader->register();
define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
$application = new Yaf_Application(dirname(APPLICATION_PATH_DIR). "/conf/application.ini");
$application->bootstrap();
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


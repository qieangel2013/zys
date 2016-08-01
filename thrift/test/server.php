<?php
define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
require_once dirname(APPLICATION_PATH). "/Thrift/ClassLoader/ThriftClassLoader.php";
$loader = new Thrift\ClassLoader\ThriftClassLoader();
$loader->registerNamespace('Thrift', dirname(APPLICATION_PATH));
$loader->registerNamespace('swoole', dirname(APPLICATION_PATH));
$loader->registerNamespace('Bin', dirname(APPLICATION_PATH));
$loader->registerDefinition('Bin',  dirname(APPLICATION_PATH));
$loader->register();
$this->application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
$this->application->bootstrap();
$config_obj=Yaf_Registry::get("config");
$rpc_config=$config_obj->rpc->toArray();
$service = new Bin\rpc\Handler();
$processor = new Bin\rpc\rpcProcessor($service);
$socket_tranport = new Thrift\Server\TServerSocket($rpc_config['ServerIp'],$rpc_config['port']);
$out_factory = $in_factory = new Thrift\Factory\TFramedTransportFactory();
$out_protocol = $in_protocol = new Thrift\Factory\TBinaryProtocolFactory();
$server = new swoole\Server($processor, $socket_tranport, $in_factory, $out_factory, $in_protocol, $out_protocol);
$server->serve();


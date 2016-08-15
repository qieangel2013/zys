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
$process = new swoole_process('rpcserver_call', true);
$pid = $process->start();
function rpcserver_call(swoole_process $worker)
{
	define('APPLICATION_PATH', dirname(dirname(__DIR__)). "/application");
	define('THRIFT_DIR_PATH',dirname(APPLICATION_PATH)."/thrift");
	require_once THRIFT_DIR_PATH. "/Thrift/ClassLoader/ThriftClassLoader.php";
	$loader = new Thrift\ClassLoader\ThriftClassLoader();
	$loader->registerNamespace('Thrift', THRIFT_DIR_PATH);
	$loader->registerNamespace('swoole', THRIFT_DIR_PATH);
	$loader->registerNamespace('Bin', THRIFT_DIR_PATH);
	$loader->registerDefinition('Bin',  THRIFT_DIR_PATH);
	$loader->register();
	$application = new Yaf_Application(dirname(APPLICATION_PATH). "/conf/application.ini");
	$application->bootstrap();
	$config_obj=Yaf_Registry::get("config");
	$rpc_config=$config_obj->rpc->toArray();
	define('SERVERIP',$rpc_config['ServerIp']);
	define('SERVERPORT',$rpc_config['port']);
	define('SERVERHOST',$rpc_config['host']);
	$service = new Bin\rpc\Handler();
	$processor = new Bin\rpc\rpcProcessor($service);
	$socket_tranport = new Thrift\Server\TServerSocket(SERVERIP,SERVERPORT);
	$out_factory = $in_factory = new Thrift\Factory\TFramedTransportFactory();
	$out_protocol = $in_protocol = new Thrift\Factory\TBinaryProtocolFactory();
	$server = new swoole\Server($processor, $socket_tranport, $in_factory, $out_factory, $in_protocol, $out_protocol);
	$server->serve();
}
swoole_process::daemon(true);
swoole_process::wait();



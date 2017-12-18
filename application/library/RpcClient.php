<?php
class RpcClient {
	private $client;
	private $message;
	private $result;
	private $transport;
	public function __construct() {
		$rpc_dir=dirname(dirname(__DIR__))."/thrift";
		$yaf_load=\Yaf\Loader::getInstance();
		$yaf_load->setLibraryPath($rpc_dir,true);
		require_once $rpc_dir. "/Thrift/ClassLoader/ThriftClassLoader.php";
		$loader = new Thrift\ClassLoader\ThriftClassLoader();
		$loader->registerNamespace('Thrift', $rpc_dir);
		$loader->registerNamespace('swoole', $rpc_dir);
		$loader->registerNamespace('Bin', $rpc_dir);
		$loader->registerDefinition('Bin', $rpc_dir);
		$loader->register();
		$config_obj=\Yaf\Registry::get("config");
		$rpc_config=$config_obj->rpc->toArray();
		$socket = new Thrift\Transport\TSocket($rpc_config['host'],$rpc_config['port']);
		$this->transport = new Thrift\Transport\TFramedTransport($socket);
		$protocol = new Thrift\Protocol\TBinaryProtocol($this->transport);
		$this->transport->open();
		$this->client = new Bin\rpc\rpcClient($protocol);
		//$yaf_load->setLibraryPath(dirname($rpc_dir).'/library',true);
	}
	public function send($data=array('name' => 'userinfo','result'=>'{"id":37936,"name"=>"zqf",email:"904208360@qq.comn"}')){

		$this->message = new Bin\rpc\Message($data);
		$this->result=$this->client->sendMessage($this->message);
	}
	public function getresult(){
		return $this->result;
	}
	public function close() {
        $this->transport->close();
    }




}

<?php
class mysqlpool
{
    private $client;
    private $msg;
    public function __construct() {
        if(extension_loaded('swoole')){
            //$this->client = new swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
            $this->client = new swoole_client(SWOOLE_SOCK_TCP);
        	$this->client->set(array(
                'socket_buffer_size'     => 1024*1024*500, 
                'open_length_check' => 1,
                'package_length_type' => 'N',
                'package_length_offset' => 0, //第N个字节是包长度的值
                'package_body_offset' => 4, //第几个字节开始计算长度
                'package_max_length' => 200000000
        	));
        }
    }
 
    public function connect($data,$type='sql') {
    	$config_obj=Yaf_Registry::get("config");
		$config=$config_obj->syncmysql->toArray();
        if(!$fp = $this->client->connect($config['ServerIp'],$config['port'],-1)) {
            return;
        }
        $senddata['type']=$type;
        $senddata['data']=$data;
        $this->send($this->packmes(json_encode($senddata,true)));
        $return_result=$this->client->recv();
        if($return_result==''){
            $return_result['success']= false;
            $return_result['error']='返回数据超时!';
            $this->msg=$return_result;
        }else{
            $this->msg=json_decode($this->unpackmes($return_result),true);
        }
        $this->client->close();
        return $this->msg;
    }
 
    public function onClose($cli) {
        $return_result['success']= false;
        $return_result['error']="Client close connection";
        $this->msg=$return_result;
    }
 
    public function onError($cli) {
        $return_result['success']= false;
        $return_result['error']=$cli->errCode;
        $this->msg=$return_result;
        $cli->close();
    }
    //包装数据
    public function packmes($data, $format = '\r\n\r\n', $preformat = '######')
    {
        //return $preformat . json_encode($data, true) . $format;
        return pack('N', strlen($data)) . $data;
    }
    
    //解包装数据
    public function unpackmes($data, $format = '\r\n\r\n', $preformat = '######')
    {
        
        $resultdata = substr($data, 4);
        return $resultdata;
    }

    public function send($data) {
        $this->client->send($data);
    }
 
    public function isConnected($cli) {
        return $this->client->isConnected();
    }
 
}

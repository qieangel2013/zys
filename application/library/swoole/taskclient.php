<?php
class swoole_taskclient
{
    private $client;
 
    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
        $this->client->on('Connect', array($this, 'onConnect'));
        $this->client->on('Receive', array($this, 'onReceive'));
        $this->client->on('Close', array($this, 'onClose'));
        $this->client->on('Error', array($this, 'onError'));
    }
 
    public function connect($data) {
        if(!$fp = $this->client->connect("192.168.1.46", 9503 , 1)) {
            echo "Error: {$fp->errMsg}[{$fp->errCode}]\n";
            return;
        }
        $this->send($data);
    }
 
    //connect之后,会调用onConnect方法
    /*public function onConnect($cli) {
        fwrite(STDOUT, "Enter Msg:");
        swoole_event_add(STDIN,function(){
            fwrite(STDOUT, "Enter Msg:");
            $msg = trim(fgets(STDIN));
            $this->send($msg);
        });
    }*/
 
    public function onClose($cli) {
        echo "Client close connection\n";
    }
 
    public function onError() {
 
    }
 
    public function onReceive($cli, $data) {
        echo "Received: ".$data."\n";
    }
 
    public function send($data) {
        $this->client->send($data);
    }
 
    public function isConnected($cli) {
        return $this->client->isConnected();
    }
 
}

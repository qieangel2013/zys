<?php
class mysql_dbclient
{
    private $client;
 
    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
        $config_obj=Yaf_Registry::get("config");
        $Serconfig=$config_obj->DbServer->toArray();
          if (!$this->client->connect($Serconfig['localip'],$Serconfig['port'], -1))
        {
            exit("connect failed. Error: {$client->errCode}\n");
        }
    }
 
    public function query($sql) {
        if($this->parseSql($sql)){
            $sqls['sql']=$sql;
            $sqls['type']=2;
        }else{
            $sqls['sql']=$sql;
            $sqls['type']=1;
        }
        $this->client->send(json_encode($sqls));
        {
            $dbclient_data=json_decode($this->client->recv(),true);
        }
        return $dbclient_data;

    }
    private function parseSql($sql){
        return preg_match("/^(\s*)select/i",$sql);
    }
    public function close() {
        $this->client->close();
    }
}

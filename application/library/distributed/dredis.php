<?php
class distributed_dredis {
	public static $instance;
	public static $redis_con;
	public function __construct() {
		$d_config=yaf_Registry::get("config");
        $config=$d_config->distributed->redis->toArray();
		self::$redis_con=new phpredis($config);
	}
	public function getname($userid){
		$where=array('id' =>$userid);
		$result =$this->user->where($where)->select();
	}
	public static function savefd($fd,$kname='fd'){
	if(self::$redis_con->listSize($kname)){
		self::$redis_con->listPush($kname,$fd,0,1);
	}else{
		self::$redis_con->listPush($kname,$fd);
	}
		//file_put_contents( __DIR__ .'/log.txt' , $fd);
	}
	public static function getfd($kname='fd'){
		$result=self::$redis_con->listGet($kname,0,-1);
		 //$m = file_get_contents( __DIR__ .'/log.txt');
		//echo $m;
		echo json_encode($result);
	}
	public static function removefd($fd,$kname='fd'){
		self::$redis_con->listRemove($kname,$fd);
	}

	public static function getInstance() {
        if (!(self::$instance instanceof distributed_dredis)) {
            self::$instance = new distributed_dredis;
        }
        return self::$instance;
    }


}

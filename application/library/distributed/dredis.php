<?php
class distributed_dredis {
	public static $instance;
	public static $redis_con;
	public function __construct() {
		$d_config=yaf_Registry::get("config");
        $config=$d_config->distributed->toArray();
        $dredis_config['server']=$config['redisserver'];
        $dredis_config['port']=$config['redisport'];
		self::$redis_con=new phpredis($dredis_config);
	}
	public function getname($userid){
		$where=array('id' =>$userid);
		$result =$this->user->where($where)->select();
	}
	public static function savefd($fd,$score,$kname='fd'){
		self::$redis_con->setAdd($kname,$fd,1,$score);
	}
	public static function getfd($kname='fd'){
		$result=self::$redis_con->setRange($kname,0,-1);
		echo json_encode($result);
	}
	public static function removefd($fd,$kname='fd'){
		self::$redis_con->setMove($kname,$fd,1);
	}

	public static function getInstance() {
        if (!(self::$instance instanceof distributed_dredis)) {
            self::$instance = new distributed_dredis;
        }
        return self::$instance;
    }


}

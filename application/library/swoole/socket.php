<?php
class swoole_socket extends Model {
	private $table="smes_user";
	public $user;
	public function __construct() {
		parent::__construct($this->table);
	}
	public function getname($userid){
		$where=array('id' =>$userid);
		$result =$this->user->where($where)->select();
	}
	public static function savefd($fd,$kname='fd'){
	$redis_con=new phpredis();
	if($redis_con->listSize($kname)){
		$redis_con->listPush($kname,$fd,0,1);
	}else{
		$redis_con->listPush($kname,$fd);
	}
	}
	public static function getfd($kname='fd'){
		$redis_con=new phpredis();
		$result=$redis_con->listGet($kname,0,-1);
		echo json_encode($result);
	}
	public static function removefd($fd,$kname='fd'){
		$redis_con=new phpredis();
		$redis_con->listRemove($kname,$fd);
	}




}

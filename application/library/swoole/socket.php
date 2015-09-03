<?php
class swoole_socket extends Model {
	private $table="hb_users";
	public $user;
	public function __construct() {
		parent::__construct($this->table);
		if (!isset($this->user)){  
            $this->user = new swoole_socket();   
        }  
	}
	public function getname($userid){
		//$where=array('id' =>37936);
		$where=array('id' =>$userid);
		$result =$this->user->where($where)->select();
	}
	public static function savefd($fd){
		//$where=array('id' =>37936);
		$redis_con=new phpredis();
		$redis_con->listPush('fd',$fd,0,1);
		//file_put_contents( __DIR__ .'/log.txt' , $fd);
	}
	public static function getfd(){
		$redis_con=new phpredis();
		$result=$redis_con->listGet('fd');
		 //$m = file_get_contents( __DIR__ .'/log.txt');
		//echo $m;
		echo $result;
	}
	public static function removefd($fd){
		$redis_con=new phpredis();
		$redis_con->listRemove('fd',$fd);
	}




}

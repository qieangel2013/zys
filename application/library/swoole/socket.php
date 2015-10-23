<?php
class swoole_socket extends Model {
	private $table="smes_user";
	public $user;
	public function __construct() {
		parent::__construct($this->table);
		/*if (!isset($this->user)){  
            $this->user = new swoole_socket();   
        }  */
	}
	public function getname($userid){
		//$where=array('id' =>37936);
		$where=array('id' =>$userid);
		$result =$this->user->where($where)->select();
	}
	public static function savefd($fd){
		//$where=array('id' =>37936);
		$redis_con=new phpredis();
	if($redis_con->listSize('fd')){
		$redis_con->listPush('fd',$fd,0,1);
	}else{
		$redis_con->listPush('fd',$fd);
	}
		//file_put_contents( __DIR__ .'/log.txt' , $fd);
	}
	public static function getfd(){
		$redis_con=new phpredis();
		$result=$redis_con->listGet('fd',0,-1);
		 //$m = file_get_contents( __DIR__ .'/log.txt');
		//echo $m;
		echo json_encode($result);
	}
	public static function removefd($fd){
		$redis_con=new phpredis();
		$redis_con->listRemove('fd',$fd);
	}




}

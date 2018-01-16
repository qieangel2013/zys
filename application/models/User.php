<?php 
use \think\Model;
class UserModel extends Model
{
	//若继承Model 必须声明表名table或者name
	protected $table = 'zs_user';
	protected $name  = 'user';
}
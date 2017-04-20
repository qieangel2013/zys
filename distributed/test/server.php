<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 文件名称：数据库连接池类
| 功能 :用户信息操作
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/6/25
|---------------------------------------------------------------
*/
function __autoload($class) {
$file =  str_replace('\\', '/',dirname(__DIR__).'/server/'.$class). '.php';
 if (is_file($file) ) {  
  require_once($file);  
 }
} 
DistributedServer::getInstance();
?>

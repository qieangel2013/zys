<?php
function __autoload($class) {
$file =  str_replace('\\', '/',dirname(__DIR__).'/server/'.$class). '.php';
 if (is_file($file) ) {  
  require_once($file);  
 }
} 
DistributedServer::getInstance();
?>

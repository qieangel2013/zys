<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/6/25
|---------------------------------------------------------------
*/
require_once str_replace('\\', '/',dirname(dirname(__DIR__)).'/distributed/server/DistributedServer.php');
DistributedServer::getInstance();
?>

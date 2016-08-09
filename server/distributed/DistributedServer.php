<?php
require_once str_replace('\\', '/',dirname(dirname(__DIR__)).'/distributed/server/DistributedServer.php');
DistributedServer::getInstance();
?>

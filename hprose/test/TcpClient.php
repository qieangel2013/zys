<?php
/**********************************************************\
|                                                          |
|                          hprose                          |
|                                                          |
| Official WebSite: http://www.hprose.com/                 |
|                   http://www.hprose.org/                 |
|                                                          |
\**********************************************************/

/**********************************************************\
 *                                                        *
 * Hprose/Socket/Client.php                               *
 *                                                        *
 * hprose socket client class for php 5.3+                *
 *                                                        *
 * LastModified: Aug 6, 2016                              *
 * Author: Ma Bingyao <andot@hprose.com>                  *
 *                                                        *
\**********************************************************/
require_once dirname(__DIR__) . '/lib/Client.php';
$client = new Client('tcp://192.168.102.163:1314', false);
echo $client->zys("zys");
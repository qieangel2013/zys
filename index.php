<?php
/* INI配置文件支持常量替换 */
define ("APPLICATION_PATH", __DIR__ . "/application");
/**
 * 默认的, Yaf_Application将会读取配置文件中在php.ini中设置的ap.environ的配置节
 * 另外在配置文件中, 可以替换PHP的常量, 比如此处的APPLICATION_PATH
 */
$application = new \Yaf\Application("conf/application.ini");
$response = $application
	->bootstrap() //可选的调用
	->run()/*执行*/;
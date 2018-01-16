<?php
/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends \Yaf\Bootstrap_Abstract{
	protected $config;
	public function _initConfig( \Yaf\Dispatcher $dispatcher )
	{
		$this->config = \Yaf\Application::app()->getConfig();
		\Yaf\Registry::set("config", $this->config);
		$dispatcher->returnResponse(true); // 开启后，不自动加载视图
		$dispatcher->catchException(true);  //开启异常捕获处理
		$dispatcher->setErrorHandler([$this,"myErrorHandler"]);
	}

	public function _initSession($dispatcher) {
		Yaf\Session::getInstance()->start();
	}

	public function _initPlugin(\Yaf\Dispatcher $dispatcher)
	{
		$AutoloadPlugin = new AutoloadPlugin();
		$dispatcher->registerPlugin($AutoloadPlugin);
	}

	public function _initBase()
	{
		// 环境常量
		defined('DEBUG') or define('DEBUG', false);
		defined('DS') or define('DS', DIRECTORY_SEPARATOR);
		define('IS_CGI', strpos(PHP_SAPI, 'cgi') === 0 ? 1 : 0);
		define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
		define('IS_MAC', strstr(PHP_OS, 'Darwin') ? 1 : 0);
		define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
		define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);
		define('NOW_TIME', $_SERVER['REQUEST_TIME']);
		define('REQUEST_METHOD', IS_CLI ? 'GET' : $_SERVER['REQUEST_METHOD']);
		define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
		define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
		define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
		define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
	}

	public function _initDb()
	{
		if(class_exists('\think\Db')){
			\think\Db::setConfig($this->config->tpdatabase->toArray());
			//Model关键字，手动加载文件
			\Yaf\Loader::import($this->config->application->directory . '/library/think/Model.php'); 
		}
	}

	function myErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		//可记录日志
		switch ($errno) {
			case YAF\ERR\NOTFOUND\CONTROLLER:
			case YAF\ERR\NOTFOUND\MODULE:
			case YAF\ERR\NOTFOUND\ACTION:
				header(" 404 Not Found");
			break;

			default:
				echo "Unknown error type: [$errno]--- $errstr ---$errfile ---- $errline  <br />\n";
			break;
		}
		return true;  //继续执行可执行的代码
	}
}

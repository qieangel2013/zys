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
		// 开始运行时间和内存使用
		define('START_TIME', microtime(true));
		define('START_MEM', memory_get_usage());
		// 系统常量
		defined('DS') or define('DS', DIRECTORY_SEPARATOR);
		defined('APP_DEBUG') or define('APP_DEBUG', false);
		defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
		defined('ROOT_PATH') or define('ROOT_PATH', dirname(APP_PATH) . DS);
		defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);
		defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'log' . DS);
		defined('CACHE_PATH') or define('CACHE_PATH', RUNTIME_PATH . 'cache' . DS);
		defined('TEMP_PATH') or define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);
		defined('EXT') or define('EXT', '.php');
		
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

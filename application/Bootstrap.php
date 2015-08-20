<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Ap调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{
	protected $config;
	public function _initSession($dispatcher) {
		Yaf_Session::getInstance()->start();
	}

	public function _initConfig() {
		$this->config = Yaf_Application::app()->getConfig();
		Yaf_Registry::set("config", $this->config);
	}
	
	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		//echo "_initRoute call second<br/>\n";
		//$router = Yaf_Dispatcher::getInstance()->getRouter();
		/**
		 * add the routes defined in ini config file
		 */
		//$router->addConfig(Yaf_Registry::get("config")->routes);
		/**
		 * test this route by access http://yourdomain.com/product/list/?/?/
		 */
		/*$route  = new Yaf_Route_Rewrite(
			"/product/list/:id/:name",
			array(
				"controller" => "product",
				"action"	 => "info",
			)
		);*/
		//$route  = new Yaf_Route_Rewrite(
			//"/User/index/:id/:name",
			//array(
				//"controller" => "User",
				//"action"	 => "index",
			//)
		//);
		//$router->addRoute('User', $route);
	}

}

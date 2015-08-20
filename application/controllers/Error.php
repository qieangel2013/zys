<?php

/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends Yaf_Controller_Abstract {
	public $actions = array(
		"action" => "actions/index.php"
	);

	public function init() {
		Yaf_Dispatcher::getInstance()->disableView();
	}
	public function errorAction($exception) {
		/* error occurs */
		switch ($exception->getCode()) {
		case YAF_ERR_NOTFOUND_MODULE:
		case YAF_ERR_NOTFOUND_CONTROLLER:
		case YAF_ERR_NOTFOUND_ACTION:
		case YAF_ERR_NOTFOUND_VIEW:
			echo 404, ":", $exception->getMessage();
			break;
		default :
			$message = $exception->getMessage();
			echo 0, ":", $exception->getMessage();
			break;
		}
	}

	public function testAction() {
	}
}

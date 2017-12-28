<?php

/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends \Yaf\Controller_Abstract {

	public function errorAction($exception)
	{
		if ($exception->getCode() > 100000) {
			//这里可以捕获到应用内抛出的异常
			echo $exception->getCode();
			echo $exception->getMessage();
			die;
		}
		switch ($exception->getCode()) {
			case 404://404
			case 515:
			case 516:
			case 517:
				//输出404
				header(getHttpStatusCode(404));
				echo '404';
				//dump($exception->getTrace());
				exit();
				break;
			default :
				echo $exception->getMessage();
				break;
		}
		throw $exception;
	}
}

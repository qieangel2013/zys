<?php

class  wx_pay_Exception extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>
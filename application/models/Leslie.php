<?php 
use Test\FooModel;
class leslieModel
{
	public function test()
	{
		return 'test';
	}

	public function foo()
	{
		return (new FooModel)->test();
	}
}
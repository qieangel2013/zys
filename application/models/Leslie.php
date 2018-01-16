<?php 
use Test\FooModel;
class leslieModel
{
	//普通DAO
	public function test()
	{
		return 'test';
	}

	public function foo()
	{
		return (new FooModel)->test();
	}
}
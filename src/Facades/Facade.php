<?php
namespace SlimGeeK\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade
{
	protected static $slim;

	public static function setFacadeApplication($app)
	{
		parent::$app = $app;
		self::$app   = $app;
		
		self::$slim = $app['slim'];
	}
}

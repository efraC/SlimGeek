<?php
namespace SlimGeek\Facades;

class View extends Facade
{
	protected static function getFacadeAccessor() { return self::$slim['view']; }
}

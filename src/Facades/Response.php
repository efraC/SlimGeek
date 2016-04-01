<?php
namespace SlimGeek\Facades;

class Response extends Facade
{
	protected static function getFacadeAccessor() { return self::$slim['response']; }
}

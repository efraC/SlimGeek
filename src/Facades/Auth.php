<?php

namespace SlimGeek\Facades;

class Auth extends Facade
{
    // return the name of the component from the DI container
    protected static function getFacadeAccessor() { return 'auth'; }
}
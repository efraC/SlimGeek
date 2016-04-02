<?php

namespace SlimGeek\Facades;

class Session extends Facade
{
    // return the name of the component from the DI container
    protected static function getFacadeAccessor() { return 'session'; }
}
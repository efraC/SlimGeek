<?php

namespace SlimGeek\Session;

class SessionManager {

    function __construct() {
        session_start();
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }else{
            return null;
        }
    }
}
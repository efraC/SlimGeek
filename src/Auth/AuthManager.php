<?php

namespace SlimGeek\Auth;

class AuthManager {

    function __construct() {
        session_start();
    }

    public function user() {
        return $_SESSION['auth.user'];
    }

    public function check() {
        return isset($_SESSION['auth.user']) ? true : false;
    }

    public function logout() {
        unset($_SESSION['auth.user']);
    }
}
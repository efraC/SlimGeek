<?php

namespace SlimGeek\Auth;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('auth', function ($app) {
            return new AuthManager();
        });
    }
}
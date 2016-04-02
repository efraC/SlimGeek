<?php

namespace SlimGeek\Session;

use Illuminate\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('session', function ($app) {
            return new SessionManager();
        });
    }
}
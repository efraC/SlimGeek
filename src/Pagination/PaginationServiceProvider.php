<?php

namespace SlimGeek\Pagination;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class PaginationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Paginator::currentPathResolver(function () {
            return $this->app['slim']->request->getResourceUri();
        });

        Paginator::currentPageResolver(function ($pageName = 'page') {
            return $this->app['slim']->request->params($pageName);
        });
    }
}

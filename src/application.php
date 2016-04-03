<?php
namespace SlimGeek;

use Closure;
use Slim\Slim;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use SlimGeek\Auth\AuthServiceProvider;
use SlimGeek\Session\SessionServiceProvider;
use SlimGeek\Pagination\PaginationServiceProvider;
use SlimGeek\Facades\Facade;

class Application extends Container
{
    protected $app;
    protected $services = array();

    public function __construct(Slim $app)
    {

        $this->registerAliases();

        $this->app = $app;

        $this->singleton('slim', function() use($app)
        {
            return $app;
        });

        $this->singleton('config', function($app)
        {
            return new Config($app['slim']);
        });

        $this['path'] = $app->config('path');


        //Antes de iniciar slim, iniciamos los servicios.
        $service_manager = $this;
        $app->hook('slim.before', function() use($service_manager)
        {
            $service_manager->boot();

        }, 1);    


        Facade::setFacadeApplication( $this );
        
        $this->registerBaseServiceProviders();  
    }
    /**
     * Boot all registered service providers
     */
    public function boot()
    {
        foreach ($this->services as $service) {
            $service->boot();
        }
    }

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));

        $this->register(new DatabaseServiceProvider($this));

        $this->register(new AuthServiceProvider($this));

        $this->register(new SessionServiceProvider($this));

        $this->register(new PaginationServiceProvider($this));
    }


    /**
     * Register a service provider with the application
     */
    public function register(ServiceProvider $service)
    {
        $this->services[] = $service;
        $service->register();
    }
    /**
     * Register services specified by class names in an array
     */
    public function registerServices(array $services)
    {
        foreach ($services as $service) {
            $this->register(new $service($this));
        }
    }
    /**
     * Overload the bind method so the services are added to the Slim DI container as well as Illuminate container
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        parent::bind($abstract, $concrete, $shared);
        $service_manager = $this;
        $this->app->$abstract = function() use($service_manager, $abstract)
        {
            return $service_manager->make($abstract);
        };
    }

    public function setConfig($config){

        foreach ($config as $key => $value) {
            $this->app->config($key, $value);
        }

        return $this;
    }


    public function run()
    {
        $this->$app->run();
    }


    /**
     * Register the core class aliases.
     *
     * @return void
     */
    public function registerAliases()
    {
        $aliases = [    
            'App'       => \SlimGeek\Facades\App::class,
            'Config'    => \SlimGeek\Facades\Config::class,
            'Request'   => \SlimGeek\Facades\Request::class,
            'Response'  => \SlimGeek\Facades\Response::class,
            'Route'     => \SlimGeek\Facades\Route::class,
            'View'      => \SlimGeek\Facades\View::class,
            'Auth'      => \SlimGeek\Facades\Auth::class,
            'Session'   => \SlimGeek\Facades\Session::class,
            'App'       => \Illuminate\Support\Facades\App::class,
            'DB'        => \Illuminate\Support\Facades\DB::class,
            'Schema'    => \Illuminate\Support\Facades\Schema::class,

        ];

        foreach ($aliases as $alias => $class) {
            class_alias($class, $alias);
        }

    }



}
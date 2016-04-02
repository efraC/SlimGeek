<?php
namespace SlimGeek\Facades;

class Route extends Facade
{

    protected static $NAMESPACE = "App\\Controllers\\";
    
    protected static function getFacadeAccessor() { return self::$slim->container['router']; }

    /**
     * Route resource to single controller
     */
    public static function resource(){
        $arguments  = func_get_args();
        $path       = $arguments[0];
        $controller = end($arguments);
        $handler    = self::$NAMESPACE.$controller;

        $resourceRoutes = array(
            'get'           => array(
                'pattern'       => "$path",
                'method'        => 'get',
                'handler'       => "$handler:index"
            ),
            'create'        => array(
                'pattern'       => "$path/create",
                'method'        => 'get',
                'handler'       => "$handler:create"
            ),
            'paginate'      => array(
                'pattern'       => "$path/page/:page",
                'method'        => 'get',
                'handler'       => "$handler:index"
            ),
            'search'        => array(
                'pattern'       => "$path/search",
                'method'        => 'get',
                'handler'       => "$handler:search"
            ),
            'post'          => array(
                'pattern'       => "$path",
                'method'        => 'post',
                'handler'       => "$handler:store"
            ),
            'show'          => array(
                'pattern'       => "$path/:id",
                'method'        => 'get',
                'handler'       => "$handler:show"
            ),
            'edit'          => array(
                'pattern'       => "$path/:id/edit",
                'method'        => 'get',
                'handler'       => "$handler:edit"
            ),
            'put'           => array(
                'pattern'       => "$path/:id",
                'method'        => 'put',
                'handler'       => "$handler:update"
            ),
            'delete'        => array(
                'pattern'       => "$path/:id",
                'method'        => 'delete',
                'handler'       => "$handler:destroy"
            )
        );

        foreach ($resourceRoutes as $route) {
            $callable   = $arguments;

            //put edited pattern to the top stack
            array_shift($callable);
            array_unshift($callable, $route['pattern']);

            //put edited controller to the bottom stack
            array_pop($callable);
            array_push($callable, $route['handler']);

            call_user_func_array(array(self::$slim, $route['method']), $callable);
        }
    }

    /**
     * Map route to all public controller method
     *
     * with
     * Route::get('/prefix', 'ClassController')
     *
     * this will map
     * GET  domain.com/prefix -> ClassController::getIndex
     * POST domain.com/prefix -> ClassCOntroller::postIndex
     * PUT  domain.com/prefix -> ClassCOntroller::putIndex
     */
    public static function controller(){

        $arguments  = func_get_args();
        $path       = $arguments[0];
        $controller = end($arguments);

        $class      = new \ReflectionClass($controller);
        $controllerMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        $uppercase  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        foreach ($controllerMethods as $method) {
            if(substr($method->name, 0, 2) != '__'){
                $methodName = $method->name;
                $callable   = $arguments;

                $pos        = strcspn($methodName, $uppercase);
                $httpMethod = substr($methodName, 0, $pos);
                $ctrlMethod = lcfirst(strpbrk($methodName, $uppercase));

                if($ctrlMethod == 'index'){
                    $pathMethod = $path;
                }else if($httpMethod == 'get'){
                    $pathMethod = "$path/$ctrlMethod(/:params+)";
                }else{
                    $pathMethod = "$path/$ctrlMethod";
                }

                //put edited pattern to the top stack
                array_shift($callable);
                array_unshift($callable, $pathMethod);

                //put edited controller to the bottom stack
                array_pop($callable);
                array_push($callable, "$controller:$methodName");

                call_user_func_array(array(self::$slim, $httpMethod), $callable);
            }
        }
    }

    public static function map()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'map'), $args);
    }

    public static function get()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'get'), $args);
    }

    public static function post()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'post'), $args);
    }

    public static function put()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'put'), $args);
    }

    public static function patch()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'patch'), $args);
    }

    public static function delete()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'delete'), $args);
    }

    public static function options()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'options'), $args);
    }

    public static function group()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'group'), $args);
    }

    public static function any()
    {
        $args = self::arguments( func_get_args() );

        return call_user_func_array(array(self::$slim, 'any'), $args);
    }

    public static function arguments( $args )
    {
        $callable = array_pop($args);

        if( is_string( $callable ) ){
            $callable = self::$NAMESPACE.$callable;
        }
        array_push( $args, $callable );

        return $args;
    }
}

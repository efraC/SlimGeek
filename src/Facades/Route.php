<?php
namespace SlimGeek\Facades;

class Route extends Facade
{

    protected static $NAMESPACE = "App\\Controllers\\";
    
	protected static function getFacadeAccessor() { return self::$slim['router']; }

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
    	return call_user_func_array(array(self::$slim, 'map'), func_get_args());
    }

    public static function get()
    {
    	return call_user_func_array(array(self::$slim, 'get'), func_get_args());
    }

    public static function post()
    {
    	return call_user_func_array(array(self::$slim, 'post'), func_get_args());
    }

    public static function put()
    {
    	return call_user_func_array(array(self::$slim, 'put'), func_get_args());
    }

    public static function patch()
    {
    	return call_user_func_array(array(self::$slim, 'patch'), func_get_args());
    }

    public static function delete()
    {
    	return call_user_func_array(array(self::$slim, 'delete'), func_get_args());
    }

    public static function options()
    {
    	return call_user_func_array(array(self::$slim, 'options'), func_get_args());
    }

    public static function group()
    {
    	return call_user_func_array(array(self::$slim, 'group'), func_get_args());
    }

    public static function any()
    {
    	return call_user_func_array(array(self::$slim, 'any'), func_get_args());
    }
}

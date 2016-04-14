<?php
namespace SlimGeek\Facades;

use Illuminate\Support\Arr;

class Request extends Facade
{
    protected static function getFacadeAccessor() { return self::$slim->container['request']; }

    public static function getParams(){
        $app = self::$slim;

        $body = json_decode( $app->request->getBody(), true ) ? json_decode( $app->request->getBody(), true ) :  [];
        $union = array_merge( $app->request->get(), $app->request->post(), $body );

        $args = func_get_args();

        switch ( func_num_args() ) {
            case 0:
                return $union;

            case 1:
                if( is_string( $args[0] ) )
                    return isset($union[ $args[0] ]) ? $union[ $args[0] ] : null;
                elseif (is_bool( $args[0] ) )
                {
                    if( $args[0] )
                        return json_decode( json_encode($union));
                    else
                        return $union;
                }

            case 2:
                if( is_string( $args[0] ) && is_string( $args[1] ) )
                    return isset($union[ $args[0] ]) ? $union[ $args[0] ] : $args[1];

        }
    }

    public static function files($name = null)
    {
        if( $name )
            return isset($_FILES[$name]) && $_FILES[$name]['size'] ? $_FILES[$name] : null;
        else
            return $_FILES;
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function all()
    {
        return self::getParams();
    }

    /**
     * Get a subset of the items from the input data.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = [];

        $input = self::all();

        foreach ($keys as $key) {
            Arr::set($results, $key, Arr::get($input, $key));
        }

        return $results;
    }

    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array  $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = self::all();

        Arr::forget($results, $keys);

        return $results;
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string|array
     */
    public function inputGet($key = null, $default = null)
    {
        $input = self::all();

        return array_get($input, $key, $default);
    }
}

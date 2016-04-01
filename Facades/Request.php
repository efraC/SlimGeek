<?php
namespace SlimGeek\Facades;

class Request extends Facade
{
	protected static function getFacadeAccessor() { return self::$slim['request']; }

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
}

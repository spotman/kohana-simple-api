<?php defined('SYSPATH') OR die('No direct script access.');


abstract class API_Types {

    const JSON_RPC = 1;

    protected static $_type_to_name = array(
        self::JSON_RPC  =>  'JSONRPC',
    );

    protected static $_type_to_url_key = array(
        self::JSON_RPC  =>  'json-rpc',
    );

    /**
     * @param int $type
     * @return string
     * @throws API_Exception
     */
    public static function type_to_name($type)
    {
        if ( ! isset(static::$_type_to_name[$type]) )
            throw new API_Exception('Undefined type :type', array(':type' => $type));

        return static::$_type_to_name[$type];
    }

    public static function type_to_url_key($type)
    {
        if ( ! isset(static::$_type_to_url_key[$type]) )
            throw new API_Exception('Undefined type: :type', array(':type' =>  $type));

        return static::$_type_to_url_key[$type];
    }

    public static function url_key_to_type($url_key)
    {
        foreach ( static::$_type_to_url_key as $type => $key )
        {
            if ( $url_key == $key )
                return $type;
        }

        throw new API_Exception('Unknown url key: :key', array(':key' =>  $url_key));
    }


}

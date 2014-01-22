<?php defined('SYSPATH') OR die('No direct script access.');


abstract class Core_API {

    const VERSION = 1;

    /**
     * API transport factory, shorthand to API_Transport::by_type()
     * @param integer|null $type Transport type constant like API_Transport::JSON_RPC
     * @return API_Server|API_Server_JSONRPC
     */
    public static function server($type = NULL)
    {
        if ( $type === NULL )
        {
            $type = static::config('server.type', API_Server::JSON_RPC);
        }

        return API_Server::by_type($type);
    }

    // TODO remove
    public static function is_server_enabled()
    {
        return (bool) static::config('server.enabled', FALSE);
    }

    /**
     * @return API_Response
     */
    public static function response()
    {
        return API_Response::factory();
    }

    /**
     * @param $name
     * @return API_Model
     */
    protected static function model($name)
    {
        return API_Model::factory($name);
    }

    /**
     * @param string $name API Model name
     * @param int|null $proxy_type Const API_Proxy::INTERNAL or API_Proxy::EXTERNAL
     * @return API_Proxy
     */
    public static function get($name, $proxy_type = NULL)
    {
        $model = static::model($name);
        $proxy = static::proxy($proxy_type);

        $proxy->model($model);

        return $proxy;
    }

    protected static function config($key, $default_value = NULL)
    {
        static $config;

        if ( $config === NULL )
        {
            $config = Kohana::config('api')->as_array();
        }

        return Arr::path($config, $key, $default_value);
    }

    /**
     * API Proxy factory
     * @param int|null $type Const API_Proxy::INTERNAL or API_Proxy::EXTERNAL
     * @return API_Proxy
     */
    protected static function proxy($type = NULL)
    {
        if ( $type === NULL )
        {
            $type = static::config('proxy', API_Proxy::INTERNAL);
        }

        return API_Proxy::by_type($type);
    }

}
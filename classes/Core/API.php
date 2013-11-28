<?php defined('SYSPATH') OR die('No direct script access.');


abstract class Core_API {

    const VERSION = 1;

    /**
     * API transport factory, shorthand to API_Transport::by_type()
     * @param integer|null $type Transport type constant like API_Transport::JSON_RPC
     * @return API_Transport|API_Transport_JsonRPC
     */
    public static function transport($type = NULL)
    {
        if ( $type === NULL )
        {
            $type = static::config()->get('transport', API_Transport::JSON_RPC);
        }

        return API_Transport::by_type($type);
    }

    public static function is_server_enabled()
    {
        return (bool) static::config()->get('server_enabled', FALSE);
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
    protected static function get($name, $proxy_type = NULL)
    {
        $model = static::model($name);
        $proxy = static::proxy($proxy_type);

        $proxy->model($model);

        return $proxy;
    }

    protected static function config()
    {
        static $config;

        if ( $config === NULL )
        {
            $config = Kohana::config('api');
        }

        return $config;
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
            $type = static::config()->get('proxy', API_Proxy::INTERNAL);
        }

        return API_Proxy::by_type($type);
    }

}
<?php

namespace Spotman\Api;

use Arr;
use Kohana;

class API
{
    // TODO ApiFactory getting config and all needed dependencies
    // TODO constructor with all dependencies (proxy)

    /**
     * API server factory, shorthand to API_Server::by_type()
     *
     * @deprecated
     *
     * @param integer|null $type Transport type constant like ApiTypesHelper::JSON_RPC
     * @param              $version
     *
     * @return \Spotman\Api\ApiServerInterface
     */
    public static function serverFactory($type, $version)
    {
        // TODO DI
        $factory = new ApiServerFactory;

        return $factory->createApiServerByType($type, $version);
    }

    /**
     * @deprecated
     * @return \Spotman\Api\ApiClientInterface
     */
    public static function clientFactory()
    {
        $type    = static::config('client.type');
        $host    = static::config('client.host');
        $version = static::config('client.version');

        $factory = new ApiClientFactory;

        return $factory->createApiClientByType($type, $host, $version);
    }

    /**
     * @param string $key
     * @param null   $default_value
     *
     * @deprecated Use BetaKiller\ConfigInterface + DI instead
     * @return string|int
     */
    protected static function config($key, $default_value = null)
    {
        static $config;

        if ($config === null) {
            $config = Kohana::config('api')->as_array();
        }

        return Arr::path($config, $key, $default_value);
    }

    /**
     * @return bool
     */
    public static function isServerEnabled()
    {
        return (bool)static::config('server.enabled', false);
    }

    /**
     * @param string   $name API Model name
     * @param int|null $proxy_type Const ApiProxy::INTERNAL or ApiProxy::EXTERNAL
     *
     * @return ApiProxy
     */
    public function get($name, $proxy_type = null)
    {
        $model = static::model($name);
        $proxy = static::proxyFactory($proxy_type);

        $proxy->setModel($model);

        return $proxy;
    }

    /**
     * @param $name
     *
     * @deprecated
     * @return \Spotman\Api\ApiModelInterface
     */
    protected static function model($name)
    {
        $factory = new ApiModelFactory();

        return $factory->create($name);
    }

    /**
     * API Proxy factory
     *
     * @param int|null $type Const API_Proxy::INTERNAL or API_Proxy::EXTERNAL
     *
     * @deprecated
     * @return ApiProxy
     */
    protected static function proxyFactory($type = null)
    {
        if ($type === null) {
            $type = (int)static::config('client.proxy', ApiProxyInterface::INTERNAL);
        }

        $factory = new ApiProxyFactory;

        return $factory->createApiProxyByType($type);
    }
}

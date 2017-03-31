<?php
namespace Spotman\Api;

use Arr;
use Kohana;

class API
{
    // TODO ApiFactory getting config and all needed dependencies
    // TODO constructor with all dependencies (proxy)

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
     * API server factory
     *
     * @param integer|null $type Transport type constant like ApiTypesHelper::JSON_RPC
     * @param              $version
     *
     * @return \Spotman\Api\ApiServerInterface
     */
    public function serverFactory($type, $version)
    {
        if (!$this->isServerEnabled()) {
            throw new ApiException('API server is not enabled');
        }

        // TODO DI
        $factory = new ApiServerFactory;

        return $factory->createApiServerByType($type, $version);
    }

    /**
     * @return bool
     */
    protected function isServerEnabled()
    {
        return (bool)static::config('server.enabled', false);
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
     * @param string   $name API Model name
     * @param int|null $proxyType Const ApiProxyInterface::INTERNAL or ApiProxyInterface::EXTERNAL
     *
     * @return \Spotman\Api\ApiProxyInterface
     */
    public function get($name, $proxyType = null)
    {
        if ($proxyType === null) {
            $proxyType = (int)static::config('client.proxy', ApiProxyInterface::INTERNAL);
        }

        $model = $this->modelFactory($name);

        return $this->proxyFactory($proxyType, $model);
    }

    /**
     * @param string $name
     *
     * @return \Spotman\Api\ApiModelInterface
     */
    protected function modelFactory($name)
    {
        $factory = new ApiModelFactory();

        return $factory->create($name);
    }

    /**
     * API Proxy factory
     *
     * @param int                            $type Const API_Proxy::INTERNAL or API_Proxy::EXTERNAL
     * @param \Spotman\Api\ApiModelInterface $model
     *
     * @return \Spotman\Api\ApiProxyInterface
     */
    protected function proxyFactory($type, ApiModelInterface $model)
    {
        $factory = new ApiProxyFactory;

        return $factory->createApiProxyByType($type, $model);
    }
}

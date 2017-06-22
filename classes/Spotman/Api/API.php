<?php
namespace Spotman\Api;

use Arr;
use Kohana;

class API
{
    /**
     * @var \Spotman\Api\ApiResourceProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var \Spotman\Api\ApiServerFactory
     */
    protected $serverFactory;

    /**
     * API constructor.
     *
     * @param \Spotman\Api\ApiResourceProxyFactory $proxyFactory
     * @param \Spotman\Api\ApiServerFactory        $serverFactory
     */
    public function __construct(ApiResourceProxyFactory $proxyFactory, ApiServerFactory $serverFactory)
    {
        $this->proxyFactory  = $proxyFactory;
        $this->serverFactory = $serverFactory;
    }

    // TODO ApiFactory getting config and all needed dependencies

    /**
     * @deprecated
     * @return \Spotman\Api\ApiClientInterface
     */
    public static function clientFactory()
    {
        $type    = static::config('client.type');
        $host    = static::config('client.host');
        $version = static::config('client.version');

        // TODO DI
        $factory = new ApiClientFactory;

        return $factory->createApiClientByType($type, $host, $version);
    }

    /**
     * @param string $key
     * @param null   $default_value
     *
     * @deprecated Use BetaKiller\ConfigProviderInterface + DI instead
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

    public static function prepareNamedArguments($classNameOrObject, $methodName, array $requestArguments)
    {
        // Skip calls without arguments
        if (!$requestArguments) {
            return $requestArguments;
        }

        // Using named arguments already, skip processing
        if (is_string(key($requestArguments))) {
            return $requestArguments;
        }

        $namedArguments = [];

        // TODO deal with missed/unordered arguments

        $reflection = new \ReflectionClass($classNameOrObject);
        $parameters = $reflection->getMethod($methodName)->getParameters();

        foreach ($parameters as $param) {
            $position = $param->getPosition();

            if (isset($requestArguments[$position])) {
                $key                  = $param->getName();
                $namedArguments[$key] = $requestArguments[$position];
            }
        }

        return $namedArguments;
    }

    /**
     * API server factory
     *
     * @param integer|null $type Transport type constant like ApiTypesHelper::JSON_RPC
     * @param              $version
     *
     * @return \Spotman\Api\ApiServerInterface
     */
    public function createServer($type, $version)
    {
        if (!$this->isServerEnabled()) {
            throw new ApiException('API server is not enabled');
        }

        return $this->serverFactory->createApiServerByType($type, $version);
    }

    /**
     * @return bool
     */
    protected function isServerEnabled()
    {
        return (bool)static::config('server.enabled', false);
    }

    /**
     * @param string   $resourceName API Model name
     * @param int|null $proxyType Const ApiResourceProxyInterface::INTERNAL or ApiResourceProxyInterface::EXTERNAL
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     */
    public function get($resourceName, $proxyType = null)
    {
        if ($proxyType === null) {
            $proxyType = (int)static::config('client.proxy', ApiResourceProxyInterface::INTERNAL);
        }

        return $this->createResourceProxy($proxyType, $resourceName);
    }

    /**
     * API Proxy factory
     *
     * @param int    $type Const API_Proxy::INTERNAL or API_Proxy::EXTERNAL
     * @param string $resourceName
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     */
    protected function createResourceProxy($type, $resourceName)
    {
        return $this->proxyFactory->createFromType($type, $resourceName);
    }
}

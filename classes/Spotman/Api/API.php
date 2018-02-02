<?php
namespace Spotman\Api;

use BetaKiller\Config\ConfigProviderInterface;

class API
{
    public const CONFIG_CLIENT_TYPE    = ['api', 'client', 'type'];
    public const CONFIG_CLIENT_HOST    = ['api', 'client', 'host'];
    public const CONFIG_CLIENT_VERSION = ['api', 'client', 'version'];
    public const CONFIG_CLIENT_PROXY   = ['api', 'client', 'proxy'];

    public const CONFIG_SERVER_ENABLED = ['api', 'server', 'enabled'];

    /**
     * @var \Spotman\Api\ApiResourceProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var \Spotman\Api\ApiServerFactory
     */
    protected $serverFactory;

    /**
     * @var \BetaKiller\Config\ConfigProviderInterface
     */
    private $configProvider;

    /**
     * API constructor.
     *
     * @param \Spotman\Api\ApiResourceProxyFactory       $proxyFactory
     * @param \Spotman\Api\ApiServerFactory              $serverFactory
     * @param \BetaKiller\Config\ConfigProviderInterface $configProvider
     */
    public function __construct(
        ApiResourceProxyFactory $proxyFactory,
        ApiServerFactory $serverFactory,
        ConfigProviderInterface $configProvider
    ) {
        $this->proxyFactory   = $proxyFactory;
        $this->serverFactory  = $serverFactory;
        $this->configProvider = $configProvider;
    }

    public static function prepareNamedArguments($classNameOrObject, string $methodName, array $requestArguments): array
    {
        // Skip calls without arguments
        if (!$requestArguments) {
            return $requestArguments;
        }

        // Using named arguments already, skip processing
        if (\is_string(key($requestArguments))) {
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
     * @throws \Spotman\Api\ApiException
     */
    public function createServer($type, $version): ApiServerInterface
    {
        return $this->serverFactory->createApiServerByType($type, $version);
    }

    /**
     * @param string   $resourceName API Model name
     * @param int|null $proxyType    Const ApiResourceProxyInterface::INTERNAL or ApiResourceProxyInterface::EXTERNAL
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     */
    public function get(string $resourceName, ?int $proxyType = null): ApiResourceProxyInterface
    {
        if ($proxyType === null) {
            $proxyType = $this->configProvider->load(self::CONFIG_CLIENT_PROXY) ?: ApiResourceProxyInterface::INTERNAL;
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
    protected function createResourceProxy(int $type, string $resourceName): ApiResourceProxyInterface
    {
        return $this->proxyFactory->createFromType($type, $resourceName);
    }
}

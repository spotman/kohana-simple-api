<?php
namespace Spotman\Api;

use BetaKiller\Config\ConfigProviderInterface;

class ApiFacade
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
     * @var \BetaKiller\Config\ConfigProviderInterface
     */
    private $configProvider;

    /**
     * API constructor.
     *
     * @param \Spotman\Api\ApiResourceProxyFactory       $proxyFactory
     * @param \BetaKiller\Config\ConfigProviderInterface $configProvider
     */
    public function __construct(
        ApiResourceProxyFactory $proxyFactory,
        ConfigProviderInterface $configProvider
    ) {
        $this->proxyFactory   = $proxyFactory;
        $this->configProvider = $configProvider;
    }

    /**
     * @param        $classNameOrObject
     * @param string $methodName
     * @param array  $requestArguments
     *
     * @return array
     * @throws \Spotman\Api\ApiException
     * @throws \ReflectionException
     */
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

        $reflection       = new \ReflectionClass($classNameOrObject);
        foreach ($reflection->getMethod($methodName)->getParameters() as $param) {
            $position = $param->getPosition();

            if (array_key_exists($position, $requestArguments)) {
                $key                  = $param->getName();
                $namedArguments[$key] = $requestArguments[$position];
            } elseif ($param->isOptional()) {
                $key                  = $param->getName();
                $namedArguments[$key] = $param->getDefaultValue();
            } else {
                $argType = $param->getType();

                // Skip parameters with class type hint coz they would be injected from DI container
                if ($argType && !$argType->isBuiltin()) {
                    continue;
                }

                throw new ApiException('Missing parameter :name for :class:::method', [
                    ':name'   => $param->getName(),
                    ':class'  => $reflection->getName(),
                    ':method' => $methodName,
                ]);
            }
        }

        return $namedArguments;
    }

    /**
     * @param string   $resourceName API Model name
     * @param int|null $proxyType    Const ApiResourceProxyInterface::INTERNAL or ApiResourceProxyInterface::EXTERNAL
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     * @throws \Spotman\Api\ApiResourceProxyException
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function get(string $resourceName, ?int $proxyType = null): ApiResourceProxyInterface
    {
        if ($proxyType === null) {
            $proxyType = (int)$this->configProvider->load(self::CONFIG_CLIENT_PROXY) ?: ApiResourceProxyInterface::INTERNAL;
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
     * @throws \Spotman\Api\ApiResourceProxyException
     * @throws \BetaKiller\Factory\FactoryException
     */
    private function createResourceProxy(int $type, string $resourceName): ApiResourceProxyInterface
    {
        return $this->proxyFactory->createFromType($type, $resourceName);
    }
}

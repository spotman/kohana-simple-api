<?php
namespace Spotman\Api;

use BetaKiller\Config\ConfigProviderInterface;

class ApiFacade
{
    public const CONFIG_GROUP          = 'api';
    public const CONFIG_CLIENT_TYPE    = ['client', 'type'];
    public const CONFIG_CLIENT_HOST    = ['client', 'host'];
    public const CONFIG_CLIENT_VERSION = ['client', 'version'];
    public const CONFIG_CLIENT_PROXY   = ['client', 'proxy'];
    public const CONFIG_SERVER_ENABLED = ['server', 'enabled'];

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
     * @param string   $resourceName API resource name
     * @param int|null $proxyType    Const ApiResourceProxyInterface::INTERNAL or ApiResourceProxyInterface::EXTERNAL
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     * @throws \Spotman\Api\ApiResourceProxyException
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function getResource(string $resourceName, ?int $proxyType = null): ApiResourceProxyInterface
    {
        if ($proxyType === null) {
            $proxyType = (int)$this->configProvider->load(ApiFacade::CONFIG_GROUP, self::CONFIG_CLIENT_PROXY)
                ?: ApiResourceProxyInterface::INTERNAL;
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

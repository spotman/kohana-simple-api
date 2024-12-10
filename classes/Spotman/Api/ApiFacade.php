<?php

namespace Spotman\Api;

use BetaKiller\Config\ConfigProviderInterface;

readonly class ApiFacade
{
    public const CONFIG_GROUP          = 'api';
    public const CONFIG_CLIENT_TYPE    = ['client', 'type'];
    public const CONFIG_CLIENT_HOST    = ['client', 'host'];
    public const CONFIG_CLIENT_VERSION = ['client', 'version'];
    public const CONFIG_CLIENT_PROXY   = ['client', 'proxy'];
    public const CONFIG_SERVER_ENABLED = ['server', 'enabled'];

    public function __construct(
        private ApiResourceProxyFactory $proxyFactory,
        private ConfigProviderInterface $configProvider
    ) {
    }

    /**
     * @param int|null $proxyType Const ApiResourceProxyInterface::INTERNAL or ApiResourceProxyInterface::EXTERNAL
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     * @throws \Spotman\Api\ApiResourceProxyException
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function getProxy(int $proxyType = null): ApiResourceProxyInterface
    {
        $proxyType ??= $this->configProvider->load(ApiFacade::CONFIG_GROUP, self::CONFIG_CLIENT_PROXY);

        return $this->proxyFactory->createFromType($proxyType ?? ApiResourceProxyInterface::INTERNAL);
    }
}

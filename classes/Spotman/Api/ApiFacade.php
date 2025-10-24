<?php

namespace Spotman\Api;

use BetaKiller\Config\ConfigProviderInterface;
use BetaKiller\Model\UserInterface;
use BetaKiller\Monitoring\MetricsCollectorInterface;
use Database_Query;

use function microtime;

readonly class ApiFacade implements ApiResourceProxyInterface
{
    public const CONFIG_GROUP          = 'api';
    public const CONFIG_CLIENT_TYPE    = ['client', 'type'];
    public const CONFIG_CLIENT_HOST    = ['client', 'host'];
    public const CONFIG_CLIENT_VERSION = ['client', 'version'];
    public const CONFIG_CLIENT_PROXY   = ['client', 'proxy'];
    public const CONFIG_SERVER_ENABLED = ['server', 'enabled'];

    public function __construct(
        private ApiResourceProxyFactory $proxyFactory,
        private ConfigProviderInterface $configProvider,
        private MetricsCollectorInterface $metrics
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

    public function call(string $resourceName, string $methodName, array $arguments, UserInterface $user): ApiMethodResponse
    {
        $callStart      = microtime(true);
        $queriesAtStart = Database_Query::getQueryCount();

        $result = $this->getProxy()->call($resourceName, $methodName, $arguments, $user);

        $queryCount = Database_Query::getQueryCount() - $queriesAtStart;
        $wallTime   = (microtime(true) - $callStart) * 1000;

        // Send metrics
        $this->metrics->increment('api.call');
        $this->metrics->increment(sprintf('api.call.user.%s', $user->getID()));
        $this->metrics->timing(sprintf('api.call.%s.%s', $resourceName, $methodName), $wallTime);
        $this->metrics->continuos('api.sql', $queryCount);
        $this->metrics->continuos(sprintf('api.sql.%s.%s', $resourceName, $methodName), $queryCount);

        return $result;
    }
}

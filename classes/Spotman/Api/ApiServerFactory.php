<?php
namespace Spotman\Api;

use BetaKiller\Config\ConfigProviderInterface;
use Psr\Container\ContainerInterface;

class ApiServerFactory
{
    /**
     * @var \BetaKiller\Config\ConfigProviderInterface
     */
    private $configProvider;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * ApiServerFactory constructor.
     *
     * @param \BetaKiller\Config\ConfigProviderInterface $configProvider
     * @param \Psr\Container\ContainerInterface          $container
     */
    public function __construct(ConfigProviderInterface $configProvider, ContainerInterface $container)
    {
        $this->configProvider = $configProvider;
        $this->container      = $container;
    }

    /**
     * @param int      $type
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiServerInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiServerByType(int $type, $version = null): ApiServerInterface
    {
        $name = ApiTypesHelper::typeToName($type);

        return $this->createApiServerByName($name, $version);
    }

    /**
     * @param string   $name
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiServerInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiServerByName(string $name, $version = null): ApiServerInterface
    {
        if (!$this->isServerEnabled()) {
            throw new ApiException('API server is not enabled');
        }

        $className = '\\Spotman\\Api\\Server\\ApiServer'.$name;

        // TODO Deal with version

        $server = $this->container->get($className);

        if (!($server instanceof ApiServerInterface)) {
            throw new ApiException('Class :class must implement :interface', [
                ':class'     => \get_class($server),
                ':interface' => ApiClientInterface::class,
            ]);
        }

        return $server;
    }

    /**
     * @return bool
     */
    protected function isServerEnabled(): bool
    {
        return (bool)$this->configProvider->load(ApiFacade::CONFIG_SERVER_ENABLED);
    }
}

<?php
namespace Spotman\Api;

use BetaKiller\Config\ConfigProviderInterface;

class ApiClientFactory
{
    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * ApiClientFactory constructor.
     *
     * @param ConfigProviderInterface $configProvider
     */
    public function __construct(ConfigProviderInterface $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param int      $type
     * @param string   $host
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiClientInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiClientByType($type, $host, $version = null): ApiClientInterface
    {
        $name = ApiTypesHelper::typeToName($type);

        $className = '\\Spotman\\Api\\Client\\ApiClient'.$name;

        if (!class_exists($className)) {
            throw new ApiException('Can not find API client for :name', [':name' => $name]);
        }

        $client = $className($type, $host, $version);

        if (!($client instanceof ApiClientInterface)) {
            throw new ApiException('Class :class must implement :interface', [
                ':class'     => \get_class($client),
                ':interface' => ApiClientInterface::class,
            ]);
        }

        return $client;
    }

    /**
     * @return \Spotman\Api\ApiClientInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createDefault(): ApiClientInterface
    {
        $type    = (string)$this->configProvider->load(ApiFacade::CONFIG_GROUP, ApiFacade::CONFIG_CLIENT_TYPE);
        $host    = (string)$this->configProvider->load(ApiFacade::CONFIG_GROUP, ApiFacade::CONFIG_CLIENT_HOST);
        $version = (string)$this->configProvider->load(ApiFacade::CONFIG_GROUP, ApiFacade::CONFIG_CLIENT_VERSION);

        return $this->createApiClientByType($type, $host, $version);
    }

    /**
     * @param string   $name
     * @param string   $host
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiClientInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiClientByName($name, $host, $version = null): ApiClientInterface
    {
        $type = ApiTypesHelper::nameToType($name);

        return $this->createApiClientByType($type, $host, $version);
    }
}

<?php
namespace Spotman\Api;

class ApiClientFactory
{
    /**
     * @param int $type
     * @param string $host
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiClientInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiClientByType($type, $host, $version = null)
    {
        $name = ApiTypesHelper::typeToName($type);

        $className = '\\Spotman\\Api\\Client\\ApiClient'.$name;

        if (!class_exists($className)) {
            throw new ApiException('Can not find API client for :name', [':name' => $name]);
        }

        $client = $className($type, $host, $version);

        if (!($client instanceof ApiClientInterface)) {
            throw new ApiException('Class :class must implement :interface', [
                ':class'    =>  get_class($client),
                ':interface' =>  ApiClientInterface::class,
            ]);
        }

        return $client;
    }

    /**
     * @param string $name
     * @param string $host
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiClientInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiClientByName($name, $host, $version = null)
    {
        $type = ApiTypesHelper::nameToType($name);
        return $this->createApiClientByType($type, $host, $version);
    }
}

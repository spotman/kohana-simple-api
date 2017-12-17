<?php
namespace Spotman\Api;

class ApiServerFactory
{
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
        $className = '\\Spotman\\Api\\Server\\ApiServer'.$name;

        // TODO Deal with version

        if (!class_exists($className)) {
            throw new ApiException('Can not find API server for :name', [':name' => $name]);
        }

        $server = new $className;

        if (!($server instanceof ApiServerInterface)) {
            throw new ApiException('Class :class must implement :interface', [
                ':class'     => \get_class($server),
                ':interface' => ApiClientInterface::class,
            ]);
        }

        return $server;
    }
}

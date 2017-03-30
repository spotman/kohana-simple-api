<?php
namespace Spotman\Api;

class ApiServerFactory
{
    /**
     * @param int $type
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiServerInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiServerByType($type, $version = null)
    {
        $name = ApiTypesHelper::typeToName($type);
        return $this->createApiServerByName($name, $version);
    }

    /**
     * @param $name
     * @param int|null $version
     *
     * @return \Spotman\Api\ApiServerInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiServerByName($name, $version = null)
    {
        $className = '\\Spotman\\Api\\Server\\ApiServer'.$name;

        // TODO Deal with version

        if (!class_exists($className)) {
            throw new ApiException('Can not find API server for :name', [':name' => $name]);
        }

        $server = new $className;

        if (!($server instanceof ApiServerInterface)) {
            throw new ApiException('Class :class must implement :interface', [
                ':class'    =>  get_class($server),
                ':interface' =>  ApiClientInterface::class,
            ]);
        }

        return $server;
    }
}

<?php
namespace Spotman\Api;

class ApiProxyFactory
{
    /**
     * @var string[]
     */
    protected $typeToName = [
        ApiProxyInterface::INTERNAL => 'Internal',
        ApiProxyInterface::EXTERNAL => 'External',
    ];

    public function createApiProxyByType($type, ApiModelInterface $model)
    {
        $name = $this->getNameFromType($type);
        return $this->createApiProxyByName($name, $model);
    }

    protected function getNameFromType($type)
    {
        if (!isset($this->typeToName[$type])) {
            throw new ApiProxyException('Invalid proxy type :value', [':value' => $type]);
        }

        return $this->typeToName[$type];
    }

    /**
     * @param string                         $name
     * @param \Spotman\Api\ApiModelInterface $model
     *
     * @return \Spotman\Api\ApiProxyInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createApiProxyByName($name, ApiModelInterface $model)
    {
        $className = '\\Spotman\\Api\\Proxy\\ApiProxy' . $name;

        if (!class_exists($className)) {
            throw new ApiException('Can not find API proxy for :name', [':name' => $name]);
        }

        $server = new $className($model);

        if (!($server instanceof ApiProxyInterface)) {
            throw new ApiException('Class :class must implement :interface', [
                ':class'     => get_class($server),
                ':interface' => ApiProxyInterface::class,
            ]);
        }

        return $server;
    }
}

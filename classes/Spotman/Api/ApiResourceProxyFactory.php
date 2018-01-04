<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactory;

class ApiResourceProxyFactory
{
    /**
     * @var string[]
     */
    protected $typeToName = [
        ApiResourceProxyInterface::INTERNAL => 'Internal',
        ApiResourceProxyInterface::EXTERNAL => 'External',
    ];

    /**
     * @var \BetaKiller\Factory\NamespaceBasedFactory
     */
    protected $factory;

    /**
     * ApiResourceProxyFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactory $factory
     */
    public function __construct(NamespaceBasedFactory $factory)
    {
        $this->factory = $factory
            ->addRootNamespace('Spotman')
            ->setExpectedInterface(ApiResourceProxyInterface::class)
            ->setClassNamespaces('Api', 'ResourceProxy')
            ->setClassSuffix('ApiResourceProxy');
    }

    /**
     * @param int    $type
     * @param string $modelName
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     */
    public function createFromType($type, $modelName)
    {
        $name = $this->getNameFromType($type);

        return $this->createFromName($name, $modelName);
    }

    protected function getNameFromType($type)
    {
        if (!isset($this->typeToName[$type])) {
            throw new ApiModelProxyException('Invalid proxy type :value', [':value' => $type]);
        }

        return $this->typeToName[$type];
    }

    /**
     * @param string $proxyName
     * @param string $modelName
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     * @throws \Spotman\Api\ApiException
     */
    public function createFromName($proxyName, $modelName)
    {
        return $this->factory->create($proxyName, ['resourceName' => $modelName]);
    }
}

<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactoryBuilder;

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
     * @param \BetaKiller\Factory\NamespaceBasedFactoryBuilder $factoryBuilder
     */
    public function __construct(NamespaceBasedFactoryBuilder $factoryBuilder)
    {
        $this->factory = $factoryBuilder
            ->createFactory()
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
     * @throws \Spotman\Api\ApiResourceProxyException
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createFromType(int $type, string $modelName): ApiResourceProxyInterface
    {
        $name = $this->getNameFromType($type);

        return $this->createFromName($name, $modelName);
    }

    /**
     * @param $type
     *
     * @return string
     * @throws \Spotman\Api\ApiResourceProxyException
     */
    protected function getNameFromType($type): string
    {
        if (!isset($this->typeToName[$type])) {
            throw new ApiResourceProxyException('Invalid proxy type :value', [':value' => $type]);
        }

        return $this->typeToName[$type];
    }

    /**
     * @param string $proxyName
     * @param string $modelName
     *
     * @return \Spotman\Api\ApiResourceProxyInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createFromName(string $proxyName, string $modelName): ApiResourceProxyInterface
    {
        return $this->factory->create($proxyName, ['resourceName' => $modelName]);
    }
}

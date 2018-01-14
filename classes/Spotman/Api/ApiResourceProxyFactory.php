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
     * @throws \Spotman\Api\ApiModelProxyException
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
     * @throws \Spotman\Api\ApiModelProxyException
     */
    protected function getNameFromType($type): string
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
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createFromName(string $proxyName, string $modelName): ApiResourceProxyInterface
    {
        return $this->factory->create($proxyName, ['resourceName' => $modelName]);
    }
}

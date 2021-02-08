<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactoryBuilderInterface;
use BetaKiller\Factory\NamespaceBasedFactoryInterface;

class ApiResourceFactory
{
    protected NamespaceBasedFactoryInterface $factory;

    /**
     * ApiResourceFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactoryBuilderInterface $factoryBuilder
     */
    public function __construct(NamespaceBasedFactoryBuilderInterface $factoryBuilder)
    {
        $this->factory = $factoryBuilder
            ->createFactory()
            ->setExpectedInterface(ApiResourceInterface::class)
            ->setClassNamespaces('Api', 'Resource')
            ->setClassSuffix(ApiResourceInterface::SUFFIX);
    }

    /**
     * @param string $name
     *
     * @return \Spotman\Api\ApiResourceInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function create(string $name): ApiResourceInterface
    {
        return $this->factory->create($name);
    }
}

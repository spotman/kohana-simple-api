<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactoryBuilder;

class ApiResourceFactory
{
    protected $factory;

    /**
     * ApiResourceFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactoryBuilder $factoryBuilder
     */
    public function __construct(NamespaceBasedFactoryBuilder $factoryBuilder)
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
    public function create($name): ApiResourceInterface
    {
        /** @var \Spotman\Api\ApiResourceInterface $resource */
        return $this->factory->create($name);
    }
}

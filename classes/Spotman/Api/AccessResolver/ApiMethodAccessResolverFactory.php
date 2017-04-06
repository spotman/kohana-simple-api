<?php
namespace Spotman\Api\AccessResolver;

use BetaKiller\Factory\NamespaceBasedFactory;

class ApiMethodAccessResolverFactory
{
    protected $factory;

    /**
     * ApiResourceFactory constructor.
     *
     * @param NamespaceBasedFactory $factory
     */
    public function __construct(NamespaceBasedFactory $factory)
    {
        $this->factory = $factory
            ->cacheInstances()
            ->addRootNamespace('Spotman')
            ->setClassPrefixes('Api', 'AccessResolver')
            ->setClassSuffix('ApiMethodAccessResolver')
            ->setExpectedInterface(ApiMethodAccessResolverInterface::class);
    }

    /**
     * @param string $name
     *
     * @return \Spotman\Api\AccessResolver\ApiMethodAccessResolverInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function create($name)
    {
        return $this->factory->create($name);
    }
}

<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactoryBuilderInterface;
use BetaKiller\Factory\NamespaceBasedFactoryInterface;

class ApiMethodFactory
{
    /**
     * @var \BetaKiller\Factory\NamespaceBasedFactoryInterface
     */
    protected NamespaceBasedFactoryInterface $factory;

    /**
     * ApiMethodFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactoryBuilderInterface $factoryBuilder
     *
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function __construct(NamespaceBasedFactoryBuilderInterface $factoryBuilder)
    {
        $this->factory = $factoryBuilder
            ->createFactory()
            ->setClassSuffix(ApiMethodInterface::SUFFIX)
            ->setExpectedInterface(ApiMethodInterface::class);
    }

    /**
     * @param string $collectionName
     * @param string $methodName
     *
     * @return ApiMethodInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createMethod(string $collectionName, string $methodName): ApiMethodInterface
    {
        $this->factory->setClassNamespaces('Api', 'Method', ucfirst($collectionName));

        return $this->factory->create(ucfirst($methodName));
    }
}

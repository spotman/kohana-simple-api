<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactoryBuilder;

class ApiMethodFactory
{
    /**
     * @var \BetaKiller\Factory\NamespaceBasedFactory
     */
    protected $factory;

    /**
     * ApiMethodFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactoryBuilder $factoryBuilder
     */
    public function __construct(NamespaceBasedFactoryBuilder $factoryBuilder)
    {
        $this->factory = $factoryBuilder
            ->createFactory()
            ->setClassSuffix(ApiMethodInterface::SUFFIX)
            ->setExpectedInterface(ApiMethodInterface::class)
            ->prepareArgumentsWith(function ($arguments, $className) {
                return ApiFacade::prepareNamedArguments($className, '__construct', $arguments);
            });
    }

    /**
     * @param string $collectionName
     * @param string $methodName
     * @param array  $arguments
     *
     * @return ApiMethodInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createMethod($collectionName, $methodName, array $arguments): ApiMethodInterface
    {
        $this->factory->setClassNamespaces('Api', 'Method', ucfirst($collectionName));

        return $this->factory->create(ucfirst($methodName), $arguments);
    }
}

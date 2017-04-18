<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactory;

class ApiMethodFactory
{
    /**
     * @var \BetaKiller\Factory\NamespaceBasedFactory
     */
    protected $factory;

    /**
     * ApiMethodFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactory $factory
     */
    public function __construct(NamespaceBasedFactory $factory)
    {
        $this->factory = $factory
            ->setClassSuffix(ApiMethodInterface::SUFFIX)
            ->setExpectedInterface(ApiMethodInterface::class)
            ->prepareArgumentsWith(function ($arguments, $className) {
                return API::prepareNamedArguments($className, '__construct', $arguments);
            });
    }

    /**
     * @param string $collectionName
     * @param string $methodName
     * @param array  $arguments
     *
     * @return ApiMethodInterface
     */
    public function createMethod($collectionName, $methodName, array $arguments)
    {
        $this->factory->setClassPrefixes('Api', 'Method', ucfirst($collectionName));

        return $this->factory->create(ucfirst($methodName), $arguments);
    }
}

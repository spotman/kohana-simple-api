<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactoryBuilder;
use BetaKiller\Model\UserInterface;

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
            ->prepareArgumentsWith(function (array $arguments, string $className) {
                return ApiFacade::prepareNamedArguments($className, '__construct', $arguments);
            });
    }

    /**
     * @param string                          $collectionName
     * @param string                          $methodName
     * @param array                           $arguments
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return ApiMethodInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createMethod(string $collectionName, string $methodName, array $arguments, UserInterface $user): ApiMethodInterface
    {
        $this->factory->setClassNamespaces('Api', 'Method', ucfirst($collectionName));

        // Inject current user into ApiMethod constructor
        $arguments['user'] = $user;

        return $this->factory->create(ucfirst($methodName), $arguments);
    }
}

<?php
namespace Spotman\Api\AccessResolver;

use BetaKiller\Factory\NamespaceBasedFactoryBuilder;
use Spotman\Api\ApiMethodInterface;

class ApiMethodAccessResolverFactory
{
    /**
     * @var \BetaKiller\Factory\NamespaceBasedFactory
     */
    private $factory;

    /**
     * @var \Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface
     */
    private $accessResolverDetector;

    /**
     * ApiResourceFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactoryBuilder                     $factoryBuilder
     * @param \Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface $detector
     */
    public function __construct(
        NamespaceBasedFactoryBuilder $factoryBuilder,
        ApiMethodAccessResolverDetectorInterface $detector
    ) {
        $this->accessResolverDetector = $detector;

        $this->factory = $factoryBuilder
            ->createFactory()
            ->cacheInstances()
            ->addRootNamespace('Spotman')
            ->setClassNamespaces('Api', 'AccessResolver')
            ->setClassSuffix('ApiMethodAccessResolver')
            ->setExpectedInterface(ApiMethodAccessResolverInterface::class);
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     *
     * @return \Spotman\Api\AccessResolver\ApiMethodAccessResolverInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createFromApiMethod(ApiMethodInterface $method): ApiMethodAccessResolverInterface
    {
        $name = $this->accessResolverDetector->detect($method);

        return $this->create($name);
    }

    /**
     * @param string $name
     *
     * @return \Spotman\Api\AccessResolver\ApiMethodAccessResolverInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    private function create(string $name): ApiMethodAccessResolverInterface
    {
        return $this->factory->create($name);
    }
}

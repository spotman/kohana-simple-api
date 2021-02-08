<?php
namespace Spotman\Api\AccessResolver;

use BetaKiller\Factory\NamespaceBasedFactoryBuilderInterface;
use BetaKiller\Factory\NamespaceBasedFactoryInterface;
use Spotman\Api\ApiMethodInterface;

class ApiMethodAccessResolverFactory
{
    /**
     * @var \BetaKiller\Factory\NamespaceBasedFactoryInterface
     */
    private NamespaceBasedFactoryInterface $factory;

    /**
     * @var \Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface
     */
    private ApiMethodAccessResolverDetectorInterface $accessResolverDetector;

    /**
     * ApiResourceFactory constructor.
     *
     * @param \BetaKiller\Factory\NamespaceBasedFactoryBuilderInterface            $factoryBuilder
     * @param \Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface $detector
     *
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function __construct(
        NamespaceBasedFactoryBuilderInterface $factoryBuilder,
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

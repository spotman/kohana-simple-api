<?php
namespace Spotman\Api\AccessResolver;

use BetaKiller\Factory\NamespaceBasedFactory;
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
     * @param \BetaKiller\Factory\NamespaceBasedFactory                            $factory
     * @param \Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface $detector
     */
    public function __construct(NamespaceBasedFactory $factory, ApiMethodAccessResolverDetectorInterface $detector)
    {
        $this->accessResolverDetector = $detector;

        $this->factory = $factory
            ->cacheInstances()
            ->addRootNamespace('Spotman')
            ->setClassPrefixes('Api', 'AccessResolver')
            ->setClassSuffix('ApiMethodAccessResolver')
            ->setExpectedInterface(ApiMethodAccessResolverInterface::class);
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     *
     * @return \Spotman\Api\AccessResolver\ApiMethodAccessResolverInterface
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function createFromApiMethod(ApiMethodInterface $method)
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
    private function create($name)
    {
        return $this->factory->create($name);
    }
}

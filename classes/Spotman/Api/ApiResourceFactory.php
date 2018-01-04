<?php
namespace Spotman\Api;

use BetaKiller\Factory\NamespaceBasedFactory;

class ApiResourceFactory
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
            ->setExpectedInterface(ApiResourceInterface::class)
            ->setClassNamespaces('Api', 'Resource')
            ->setClassSuffix(ApiResourceInterface::SUFFIX);
    }

    /**
     * @param string $name
     *
     * @return \Spotman\Api\ApiResourceInterface
     * @throws \Spotman\Api\ApiMethodException
     */
    public function create($name)
    {
        /** @var \Spotman\Api\ApiResourceInterface $resource */
        return $this->factory->create($name);
    }
}

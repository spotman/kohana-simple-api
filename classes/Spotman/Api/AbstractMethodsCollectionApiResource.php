<?php
namespace Spotman\Api;

abstract class AbstractMethodsCollectionApiResource extends AbstractApiResource implements ApiMethodsCollectionInterface
{
    /**
     * @var \Spotman\Api\API
     */
    protected $api;

    /**
     * AbstractMethodsCollectionApiResource constructor.
     *
     * @param \Spotman\Api\API $api
     */
    public function __construct(API $api)
    {
        $this->api = $api;
    }

    /**
     * Allow magic calls
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    final public function __call($name, array $arguments)
    {
        return $this->call($name, $arguments);
    }

    /**
     * Manually call ApiMethod from MethodsCollection method-helper
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    protected function call($name, array $arguments): ApiMethodResponse
    {
        // Forward call to API subsystem
        return $this->api->get($this->getName())->call($name, $arguments);
    }
}


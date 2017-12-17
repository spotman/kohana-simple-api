<?php
namespace Spotman\Api\ResourceProxy;

use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiResourceProxyInterface;

abstract class AbstractApiResourceProxy implements ApiResourceProxyInterface
{
    /**
     * @var string
     */
    protected $resourceName;

    /**
     * AbstractApiResourceProxy constructor.
     *
     * @param string $resourceName
     */
    public function __construct(string $resourceName)
    {
        $this->resourceName = $resourceName;
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return ApiMethodResponse
     */
    final public function __call($methodName, $arguments): ApiMethodResponse
    {
        return $this->call($methodName, $arguments);
    }
}

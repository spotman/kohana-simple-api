<?php
namespace Spotman\Api\ResourceProxy;

use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiModelProxyException;
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
     * @throws \Spotman\Api\ApiMethodException
     * @throws \Spotman\Api\ApiModelProxyException
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    final public function __call($methodName, $arguments): ApiMethodResponse
    {
        return $this->call($methodName, $arguments);
    }

    /**
     * Simple proxy call to model method
     *
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     * @throws \Spotman\Api\ApiMethodException
     * @throws \Spotman\Api\ApiAccessViolationException
     * @throws \Spotman\Api\ApiModelProxyException
     */
    final public function call(string $methodName, array $arguments): ApiMethodResponse
    {
        $response = $this->callResourceMethod($methodName, $arguments);

        // For methods with empty response
        if ($response === null) {
            $response = ApiMethodResponse::factory();
        }

        if (!($response instanceof ApiMethodResponse)) {
            throw new ApiModelProxyException('Api model method [:model.:method] must return :must or null', [
                ':model'  => $this->resourceName,
                ':method' => $methodName,
                ':must'   => ApiMethodResponse::class,
            ]);
        }

        return $response;
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    abstract protected function callResourceMethod(string $methodName, array $arguments): ?ApiMethodResponse;
}

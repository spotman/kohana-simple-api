<?php
namespace Spotman\Api;

interface ApiResourceProxyInterface
{
    public const INTERNAL = 1;
    public const EXTERNAL = 2;

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return ApiMethodResponse
     */
    public function __call($methodName, $arguments): ApiMethodResponse;

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     */
    public function call(string $methodName, array $arguments): ApiMethodResponse;
}

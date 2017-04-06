<?php
namespace Spotman\Api;

interface ApiResourceProxyInterface
{
    const INTERNAL = 1;
    const EXTERNAL = 2;

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return ApiMethodResponse
     */
    public function __call($methodName, $arguments);

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     */
    public function call($methodName, array $arguments);
}

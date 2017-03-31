<?php
namespace Spotman\Api;

interface ApiProxyInterface
{
    const INTERNAL = 1;
    const EXTERNAL = 2;

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiModelResponse
     */
    public function __call($method, $arguments);
}

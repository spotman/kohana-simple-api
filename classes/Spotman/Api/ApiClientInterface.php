<?php
namespace Spotman\Api;

interface ApiClientInterface
{
    /**
     * @param string $resource
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiMethodResponse
     */
    public function remote_procedure_call(
        string $resource,
        string $method, array $arguments
    ): ApiMethodResponse;
}

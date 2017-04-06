<?php
namespace Spotman\Api;

interface ApiClientInterface
{
    /**
     * @param string $resource
     * @param string $method
     * @param array $arguments
     * @return \Spotman\Api\ApiMethodResponse
     */
    public function remote_procedure_call($resource, $method, array $arguments);
}

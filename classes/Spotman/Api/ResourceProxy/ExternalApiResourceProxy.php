<?php
namespace Spotman\Api\ResourceProxy;

use Spotman\Api\API;
use Spotman\Api\ApiMethodResponse;

class ExternalApiResourceProxy extends AbstractApiResourceProxy
{
    /**
     * Performs remote API call
     *
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     */
    public function call(string $methodName, array $arguments): ApiMethodResponse
    {
        $client = API::clientFactory();

        return $client->remote_procedure_call($this->resourceName, $methodName, $arguments);
    }
}

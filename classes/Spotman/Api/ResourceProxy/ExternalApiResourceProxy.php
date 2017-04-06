<?php
namespace Spotman\Api\ResourceProxy;

use Spotman\Api\API;

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
    public function call($methodName, array $arguments)
    {
        $client = API::clientFactory();

        return $client->remote_procedure_call($this->resourceName, $methodName, $arguments);
    }
}

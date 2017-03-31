<?php
namespace Spotman\Api\Proxy;

use Spotman\Api\API;
use Spotman\Api\ApiModelResponse;

class ApiProxyExternal extends ApiProxyAbstract
{
    /**
     * Performs remote API call
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiModelResponse Result of the API call
     */
    protected function call($method, array $arguments)
    {
        $client   = API::clientFactory();
        $resource = $this->model->getName();

        return $client->remote_procedure_call($resource, $method, $arguments);
    }
}

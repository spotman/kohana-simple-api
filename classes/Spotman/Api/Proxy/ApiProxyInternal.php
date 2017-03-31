<?php
namespace Spotman\Api\Proxy;

use Spotman\Api\ApiModelResponse;

class ApiProxyInternal extends ApiProxyAbstract
{
    /**
     * Simple proxy call to model method
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiModelResponse Result of the API call
     */
    protected function call($method, array $arguments)
    {
        return $this->callModelMethod($method, $arguments);
    }
}

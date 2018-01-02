<?php
namespace Spotman\Api\Client;

use JSONRPC_Client;
use Spotman\Api\ApiException;
use Spotman\Api\ApiMethodResponse;

class ApiClientJsonRpc extends ApiClientAbstract
{
    /**
     * @param string $resource
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiMethodResponse
     * @throws \Spotman\Api\ApiException
     */
    public function remote_procedure_call(string $resource, string $method, array $arguments): ApiMethodResponse
    {
        $url    = $this->get_url();
        $client = JSONRPC_Client::factory();

        try {
            $data = $client->call($url, $resource.'.'.$method, $arguments);
        } catch (\Throwable $e) {
            throw new ApiException(':error', [':error' => $e->getMessage()], $e->getCode(), $e);
        }

        $lastModified = $client->get_last_modified();

        return ApiMethodResponse::factory($data, $lastModified);
    }
}

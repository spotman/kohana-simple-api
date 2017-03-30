<?php

namespace Spotman\Api\Client;

use Spotman\Api\Client\ApiClientAbstract;
use Exception;
use JSONRPC_Client;
use Spotman\Api\ApiException;
use Spotman\Api\ApiModelResponse;

class ApiClientJsonRpc extends ApiClientAbstract
{
    /**
     * @param       $resource
     * @param       $method
     * @param array $arguments
     *
     * @return ApiModelResponse
     * @throws ApiException
     */
    public function remote_procedure_call($resource, $method, array $arguments)
    {
        $url    = $this->get_url();
        $client = JSONRPC_Client::factory();

        try {
            $data = $client->call($url, $resource . '.' . $method, $arguments);
        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), NULL, $e);
        }

        $lastModified = $client->get_last_modified();

        return ApiModelResponse::factory($data, $lastModified);
    }
}

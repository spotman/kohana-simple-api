<?php

namespace Spotman\Api\Server;

use JSONRPC_Server;
use Request;
use Response;
use Spotman\Api\ApiModelFactory;

class ApiServerJsonRpc extends ApiServerAbstract
{
    /**
     * Process API request and push data to $response
     *
     * @param Request  $request
     * @param Response $response
     */
    public function process(\Request $request, \Response $response)
    {
        $server = JSONRPC_Server::factory($response);

        $server->register_proxy_factory(function ($resourceName) {
            $factory = new ApiModelFactory;

            return $factory->create($resourceName);
        });

        $server->process();
    }
}

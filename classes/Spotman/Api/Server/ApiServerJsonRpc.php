<?php
namespace Spotman\Api\Server;

use Spotman\Api\API;
use Spotman\Api\ApiProxy;
use JSONRPC_Server;
use Request;
use Response;
use Spotman\Api\ApiProxyInterface;

class ApiServerJsonRpc extends ApiServerAbstract
{
    /**
     * Process API request and push data to $response
     *
     * @param \Spotman\Api\API $api
     * @param Request          $request
     * @param Response         $response
     */
    public function process(API $api, \Request $request, \Response $response)
    {
        $server = JSONRPC_Server::factory($response);

        $server->register_proxy_factory(function($resourceName) use ($api) {
            // Force internal request
            return $api->get($resourceName, ApiProxyInterface::INTERNAL);
        });

        $server->process();
    }
}

<?php
namespace Spotman\Api\Server;

use JSONRPC_Server;
use Request;
use Response;
use Spotman\Api\API;
use Spotman\Api\ApiAccessViolationException;

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

        $server->add_access_violation_exception(ApiAccessViolationException::class);

        $server->register_proxy_factory(function ($resourceName) use ($api) {
            return $api->get($resourceName);
        });

        $server->process();
    }
}

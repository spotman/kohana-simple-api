<?php defined('SYSPATH') OR die('No direct script access.');

class Core_API_Server_JSONRPC extends API_Server {

    /**
     * Process API request and push data to $response
     *
     * @param Request $request
     * @param Response $response
     */
    public function process(Request $request, Response $response)
    {
        JSONRPC_Server::factory($response)
            ->register_proxy_factory('API::get')
            ->process();
    }

}
<?php
namespace Spotman\Api;

interface ApiServerInterface
{
    /**
     * Process API request and push data to $response
     *
     * @param \Spotman\Api\API $api
     * @param \Request         $request
     * @param \Response        $response
     *
     * @return void
     * @todo Remove dependencies for Kohana request and response (create 2 interfaces + 2 adapters for Kohana)
     * @todo Deal with Kohana-dependent JSONRPC module (rewrite it or drop it in favour of external library)
     */
    public function process(API $api, \Request $request, \Response $response): void;
}

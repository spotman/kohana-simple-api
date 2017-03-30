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
     * @return
     */
    public function process(API $api, \Request $request, \Response $response);
}

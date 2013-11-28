<?php defined('SYSPATH') OR die('No direct script access.');


class Core_API_Transport_JsonRPC extends API_Transport {

    public function remote_procedure_call($resource, $method, $arguments)
    {
        // TODO call to remote machine via JSON-RPC library
        throw new HTTP_Exception_501('Not implemented yet');
    }

}

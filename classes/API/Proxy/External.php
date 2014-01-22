<?php defined('SYSPATH') OR die('No direct script access.');

class API_Proxy_External extends API_Proxy {

    protected function call($method, array $arguments)
    {
        // TODO change to API_Client
        // Getting transport
        $transport = API::server();

        $resource = $this->model()->name();

        return $transport->remote_procedure_call($resource, $method, $arguments);
    }

}
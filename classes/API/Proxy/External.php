<?php defined('SYSPATH') OR die('No direct script access.');

class API_Proxy_External extends API_Proxy {

    /**
     * Performs remote API call
     *
     * @param string $method
     * @param array $arguments
     * @return array Result of the API_Response::as_array()
     */
    protected function call($method, array $arguments)
    {
        $client = API::client();
        $resource = $this->model()->name();

        return $client->remote_procedure_call($resource, $method, $arguments)->as_array();
    }

}
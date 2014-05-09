<?php defined('SYSPATH') OR die('No direct script access.');

class API_Proxy_Internal extends API_Proxy {

    /**
     * Simple proxy call to model method
     *
     * @param string $method
     * @param array $arguments
     * @return array Result of the API_Response::as_array()
     */
    protected function call($method, array $arguments)
    {
        return $this->model_call($method, $arguments)->as_array();
    }

}
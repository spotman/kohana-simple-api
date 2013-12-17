<?php defined('SYSPATH') OR die('No direct script access.');

class API_Proxy_Internal extends API_Proxy {

    /**
     * Simple proxy call to model method
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    protected function call($method, array $arguments)
    {
        return $this->model_call($method, $arguments);
    }

}
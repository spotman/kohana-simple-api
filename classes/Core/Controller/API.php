<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Core_Controller_API extends Controller_Proxy {

    protected $_transport;

    protected $_transport_request_key_to_const = array(
        'json'  =>  API_Transport::JSON_RPC
    );

    protected function _init()
    {
        if ( ! API::is_server_enabled() )
            throw new HTTP_Exception_501('API is not implemented');

        // Getting current transport
        $this->_transport = $this->get_transport();
    }

    protected function get_transport()
    {
        $transport_key = $this->request->param('transport');
        $transport_type = $this->get_transport_type_by_route_key($transport_key);

        return API::transport($transport_type);
    }

    protected function get_transport_type_by_route_key($key)
    {
        if ( ! isset($this->_transport_request_key_to_const[ $key ]) )
            throw new API_Exception('Unknown transport key: :key', array(':key' =>  $key));

        return $this->_transport_request_key_to_const[ $key ];
    }

    protected function get_proxy_object()
    {
        // TODO get model class name from transport
    }

    /**
     * @return string
     */
    protected function get_proxy_method()
    {
        // TODO get model class name from transport
        return $this->request->param('method');
    }

}
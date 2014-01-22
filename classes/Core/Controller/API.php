<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Core_Controller_API extends Controller {

    /**
     * @var API_Server
     */
    protected $_server;

    protected $_server_request_key_to_const = array(
        'json-rpc'  =>  API_Server::JSON_RPC
    );

    public function before()
    {
        if ( ! API::is_server_enabled() )
            throw new HTTP_Exception_501('API is not implemented');

        // Getting current server
        $this->_server = $this->get_server();
    }

    protected function get_server()
    {
        $server_key = $this->request->param('server');
        $server_type = $this->get_server_type_by_route_key($server_key);

        return API::server($server_type);
    }

    protected function get_server_type_by_route_key($key)
    {
        if ( ! isset($this->_server_request_key_to_const[ $key ]) )
            throw new API_Exception('Unknown server key: :key', array(':key' =>  $key));

        return $this->_server_request_key_to_const[ $key ];
    }

    public function action_process()
    {
        $this->_server->process($this->request, $this->response);
    }

}
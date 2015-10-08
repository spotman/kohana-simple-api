<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Core_Controller_API extends Controller {

    /**
     * @var API_Server|API_Server_JSONRPC
     */
    protected $_server;

    public function before()
    {
        if ( ! API::is_server_enabled() )
            throw new HTTP_Exception_501('API is not implemented');

        // Getting current server
        $this->_server = $this->get_server();
    }

    protected function get_server()
    {
        $server_key = $this->request->param('type');
        $server_type = $this->get_server_type_by_route_key($server_key);

        $server_version = (int) $this->request->param('version');

        return API::server($server_type, $server_version);
    }

    protected function get_server_type_by_route_key($key)
    {
        return API_Server::url_key_to_type($key);
    }

    public function action_process()
    {
        $this->_server->process($this->request, $this->response);
    }

}

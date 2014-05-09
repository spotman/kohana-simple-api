<?php defined('SYSPATH') OR die('No direct script access.');

class Core_API_Client_JSONRPC extends API_Client {

    /**
     * @param $resource
     * @param $method
     * @param array $arguments
     * @return API_Response
     * @throws API_Exception
     */
    public function remote_procedure_call($resource, $method, array $arguments)
    {
        $url = $this->get_url();
        $client = JSONRPC_Client::factory();

        try
        {
            $data = $client->call($url, $resource.'.'.$method, $arguments);
        }
        catch ( Exception $e )
        {
            throw new API_Exception($e->getMessage(), NULL, $e);
        }

        $last_modified = $client->get_last_modified();

        return API::response()
            ->set_data($data)
            ->set_last_modified($last_modified);
    }

}
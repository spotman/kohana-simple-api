<?php defined('SYSPATH') OR die('No direct script access.');

return array(

    /**
     * Defining API client
     */
    'client'        =>  array(

        /**
         * What version of the API we use
         */
        'version'   =>  1,

        /**
         * How do we connect to API
         * API_Proxy::INTERNAL - directly call to API_Model_...
         * API_Proxy::EXTERNAL - through HTTP-request to remote API server
         */
        'proxy'     =>  API_Proxy::INTERNAL,

        /**
         * Options for remote API server
         */

        /**
         * Hostname of the API server
         */
        'host'      =>  'api.example.com',

        /**
         * Server type
         */
        'type'      =>  API_Server::JSON_RPC,

    ),

    /**
     * Defining API server
     */
    'server'    => array(

        /**
         * If TRUE then API server is working
         */
        'enabled'   =>  TRUE,
    ),
);
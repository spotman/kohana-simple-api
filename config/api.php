<?php

use Spotman\Api\ApiProxy;
use Spotman\Api\ApiProxyInterface;
use Spotman\Api\ApiTypesHelper;

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
         * ApiProxy::INTERNAL - directly call to API_Model_...
         * ApiProxy::EXTERNAL - through HTTP-request to remote API server
         */
        'proxy'     => ApiProxyInterface::INTERNAL,

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
        'type'      =>  ApiTypesHelper::JSON_RPC,

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

<?php

use Spotman\Api\ApiProxyInterface;
use Spotman\Api\ApiTypesHelper;

return [

    /**
     * Defining API client
     */
    'client' => [

        /**
         * What version of the API we use
         */
        'version' => 1,

        /**
         * How do we connect to API
         * ApiProxyInterface::INTERNAL - directly call to API_Model_...
         * ApiProxyInterface::EXTERNAL - through HTTP-request to remote API server
         */
        'proxy'   => ApiProxyInterface::INTERNAL,

        /**
         * Options for remote API server
         */

        /**
         * Hostname of the API server
         */
        'host'    => 'api.example.com',

        /**
         * Server type
         */
        'type'    => ApiTypesHelper::JSON_RPC,

    ],

    /**
     * Defining API server
     */
    'server' => [

        /**
         * If TRUE then API server is working
         */
        'enabled' => true,
    ],
];

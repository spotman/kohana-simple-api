<?php defined('SYSPATH') OR die('No direct script access.');

// JSON RPC API for external calls
Route::set('api', 'api/v<version>/<server>', array('version' => '[0-9]+', 'server' => 'json-rpc'))
    ->defaults(array(
        'module'        => 'api',
        'controller'    => 'API',
        'action'        => 'process',
    ));
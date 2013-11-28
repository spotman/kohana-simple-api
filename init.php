<?php defined('SYSPATH') OR die('No direct script access.');

// JSON RPC API for external calls
Route::set('api', 'api/v<version>/<transport>', array('version' => '[0-9]+', 'transport' => 'json'))
    ->defaults(array(
        'module'        => 'api',
        'controller'    => 'API',
    ));
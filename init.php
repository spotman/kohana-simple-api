<?php defined('SYSPATH') OR die('No direct script access.');

Route::set('api', 'api/v<version>/<transport>(/<resource>)', array('version' => '[0-9]+', 'transport' => '[a-z]+'))
    ->defaults(array(
        'module'        => 'api',
//        'directory'     => 'Error',
        'controller'    => 'API',
    ));
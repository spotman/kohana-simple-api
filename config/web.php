<?php
declare(strict_types=1);

use BetaKiller\Config\WebConfig;
use Spotman\Api\ApiRequestHandler;

return [
    WebConfig::KEY_ROUTES => [
        WebConfig::KEY_POST => [
            // API HTTP gate
            '/api/v{version:\d+}/{type:.+}' => ApiRequestHandler::class,
        ],
    ],
];

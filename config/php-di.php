<?php

use Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface;
use Spotman\Api\AccessResolver\DefaultApiMethodAccessResolverDetector;

return [

    'definitions' => [

        // Basic access resolver detector
        ApiMethodAccessResolverDetectorInterface::class => DI\get(DefaultApiMethodAccessResolverDetector::class),

    ],

];

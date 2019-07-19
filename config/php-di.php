<?php

use Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface;
use Spotman\Api\AccessResolver\DefaultApiMethodAccessResolverDetector;
use Spotman\Api\ApiMethodResponseConverter;
use Spotman\Api\ApiMethodResponseConverterInterface;

return [

    'definitions' => [

        // Basic access resolver detector
        ApiMethodAccessResolverDetectorInterface::class => DI\autowire(DefaultApiMethodAccessResolverDetector::class),

        ApiMethodResponseConverterInterface::class => DI\autowire(ApiMethodResponseConverter::class),
    ],

];

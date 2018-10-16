<?php

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Spotman\Api\AccessResolver\ApiMethodAccessResolverDetectorInterface;
use Spotman\Api\AccessResolver\DefaultApiMethodAccessResolverDetector;
use Spotman\Api\ApiAccessViolationException;
use Spotman\Api\ApiFacade;
use Spotman\Api\JsonRpc\JsonRpcServer;

return [

    'definitions' => [

        // Basic access resolver detector
        ApiMethodAccessResolverDetectorInterface::class => DI\autowire(DefaultApiMethodAccessResolverDetector::class),

        JsonRpcServer::class => DI\factory(function (
            ResponseFactoryInterface $responseFactory,
            ApiFacade $api,
            LoggerInterface $logger
        ) {
            $server = new JsonRpcServer($responseFactory, $logger);

            $server->addAccessViolationException(ApiAccessViolationException::class);

            $server->registerProxyFactory(function (string $resourceName) use ($api) {
                return $api->get($resourceName);
            });
        }),
    ],

];

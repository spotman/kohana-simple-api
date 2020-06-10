<?php
declare(strict_types=1);

namespace Spotman\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApiRequestHandler implements RequestHandlerInterface
{
    /**
     * @var \Spotman\Api\ApiServerFactory
     */
    private $serverFactory;

    /**
     * ApiRequestHandler constructor.
     *
     * @param \Spotman\Api\ApiServerFactory $serverFactory
     */
    public function __construct(ApiServerFactory $serverFactory)
    {
        $this->serverFactory = $serverFactory;
    }

    /**
     * Handle the request and return a response.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = Route::fromRequest($request);

        $server = $this->serverFactory->createApiServerByType($route->getType());

        return $server->handle(
            $request->withAttribute(ApiServerInterface::API_VERSION_REQUEST_ATTR, $route->getVersion())
        );
    }
}

<?php
namespace Spotman\Api\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spotman\Api\JsonRpc\JsonRpcServer;

class ApiServerJsonRpc extends ApiServerAbstract
{
    /**
     * @var \Spotman\Api\JsonRpc\JsonRpcServer
     */
    private $server;

    /**
     * ApiServerJsonRpc constructor.
     *
     * @param \Spotman\Api\JsonRpc\JsonRpcServer $server
     */
    public function __construct(JsonRpcServer $server)
    {
        $this->server = $server;
    }

    /**
     * Process API request and push data to $response
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->server->handle($request);
    }
}

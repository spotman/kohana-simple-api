<?php
namespace Spotman\Api\Client;

use BetaKiller\Model\UserInterface;
use Spotman\Api\ApiException;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\JsonRpc\JsonRpcClient;
use Spotman\Api\Route;

final class ApiClientJsonRpc extends ApiClientAbstract
{
    /**
     * @param string                          $resource
     * @param string                          $method
     * @param array                           $arguments
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return ApiMethodResponse
     * @throws \Spotman\Api\ApiException
     */
    public function remoteProcedureCall(
        string $resource,
        string $method,
        array $arguments,
        UserInterface $user
    ): ApiMethodResponse {
        $url    = $this->getUrl();
        $client = JsonRpcClient::factory();

        try {
            $response = $client->call($url, $resource.'.'.$method, $arguments);

            return ApiMethodResponse::ok($response->getData(), $response->getLastModified());
        } catch (\Throwable $e) {
            throw new ApiException(':error', [':error' => $e->getMessage()], $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    private function getUrl(): string
    {
        $route = new Route($this->version, $this->type);

        return $this->host.$route;
    }
}

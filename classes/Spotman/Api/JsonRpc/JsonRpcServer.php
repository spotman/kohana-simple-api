<?php
namespace Spotman\Api\JsonRpc;

use BetaKiller\Auth\AccessDeniedException;
use BetaKiller\Exception\HttpExceptionInterface;
use BetaKiller\Helper\LoggerHelperTrait;
use BetaKiller\Helper\ServerRequestHelper;
use BetaKiller\Model\UserInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Spotman\Api\ApiAccessViolationException;
use Spotman\Api\ApiFacade;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\JsonRpc\Exception\HttpJsonRpcException;
use Spotman\Api\JsonRpc\Exception\InternalErrorJsonRpcException;
use Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException;
use Throwable;

final class JsonRpcServer implements RequestHandlerInterface
{
    use LoggerHelperTrait;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Spotman\Api\ApiFacade
     */
    private $api;

    public function __construct(ApiFacade $api, ResponseFactoryInterface $responseFactory, LoggerInterface $logger)
    {
        $this->api             = $api;
        $this->responseFactory = $responseFactory;
        $this->logger          = $logger;
    }

    public function handle(ServerRequestInterface $httpRequest): ResponseInterface
    {
        $response     = null;
        $lastModified = null;

        try {
            $rawBody = $httpRequest->getBody()->getContents();

            if (!$rawBody) {
                throw new InvalidRequestJsonRpcException;
            }

            $body = \json_decode($rawBody);
            $user = ServerRequestHelper::getUser($httpRequest);

            if (\is_array($body)) {
                $batchData    = $this->processBatch($body, $user);
                $batchResults = [];

                // Update last modified for each item
                foreach ($batchData as $item) {
                    $lastModified   = $this->updateLastModified($lastModified, $item->getLastModified());
                    $batchResults[] = $item->body();
                }

                $rpcResponse = '['.implode(',', array_filter($batchResults)).']';
            } else {
                $request = new JsonRpcServerRequest($body);

                $data = $this->processRequest($request, $user);

                $lastModified = $this->updateLastModified($lastModified, $data->getLastModified());
                $rpcResponse  = $data->body();
            }
        } catch (\Throwable $e) {
            $this->processException($e);

            $e = $this->wrapException($e);

            $rpcResponse = JsonRpcServerResponse::factory()->failed($e)->body();
        }

        // Send response
        return $this->makeResponse($rpcResponse, $lastModified);
    }

    private function isAccessViolationException(\Throwable $e): bool
    {
        return $e instanceof ApiAccessViolationException;
    }

    /**
     * @param array                           $batchRequest
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\JsonRpc\JsonRpcServerResponse[]
     * @throws \Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException
     */
    private function processBatch(array $batchRequest, UserInterface $user): array
    {
        $results = [];

        // Process each request
        foreach ($batchRequest as $subRequest) {
            $request   = new JsonRpcServerRequest($subRequest);
            $results[] = $this->processRequest($request, $user);
        }

        return array_filter($results);
    }

    /**
     * @param JsonRpcServerRequest            $request
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\JsonRpc\JsonRpcServerResponse
     * @throws \Exception
     */
    private function processRequest(JsonRpcServerRequest $request, UserInterface $user): JsonRpcServerResponse
    {
        // Make response
        $response = JsonRpcServerResponse::factory()->setId($request->getId());

        $lastModified = new DateTimeImmutable;

        try {
            // Get class/method names
            $resourceName = $request->getResourceName();
            $methodName   = $request->getMethodName();

            // Call proxy object method
            $result = $this->api
                ->getResource($resourceName)
                ->call($methodName, $request->getParams(), $user);

            if (\is_object($result) && $result instanceof ApiMethodResponse) {
                $lastModified = $result->getLastModified();
                $result       = $result->getData();
            }

            // Make response
            $response->succeeded($result)->setLastModified($lastModified);
        } catch (Throwable $e) {
            $this->processException($e);
            $e = $this->wrapException($e);
            $response->failed($e);
        }

        return $response;
    }

    private function wrapException(\Throwable $e)
    {
        if ($this->isAccessViolationException($e)) {
            // Access violation, throw 403
            $e = new AccessDeniedException();
        }

        $message = 'Internal error';

        if ($e instanceof HttpExceptionInterface) {
            // Common HTTP exception (transfers HTTP code to response)
            $e = new HttpJsonRpcException($message, $e);
        } elseif (!$e instanceof JsonRpcException) {
            // Wrap unknown exception into InternalError
            $e = new InternalErrorJsonRpcException($message, null, $e);
        }

        return $e;
    }

    private function updateLastModified(?DateTimeImmutable $current, ?DateTimeImmutable $updated): ?DateTimeImmutable
    {
        if ($updated && $updated > $current) {
            $current = $updated;
        }

        return $current;
    }

    /**
     * Process exception logging, notifications, etc
     *
     * @param \Throwable $e
     *
     * @return void
     */
    private function processException(\Throwable $e): void
    {
        $this->logException($this->logger, $e);
    }

    private function makeResponse(string $rpcResponse, ?DateTimeInterface $lastModified = null): ResponseInterface
    {
        if (!$lastModified) {
            $lastModified = new DateTimeImmutable;
        }

        $value = gmdate("D, d M Y H:i:s \G\M\T", $lastModified->getTimestamp());

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($rpcResponse);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Last-Modified', $value);
    }
}

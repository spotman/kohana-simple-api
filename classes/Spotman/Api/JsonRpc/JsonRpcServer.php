<?php
namespace Spotman\Api\JsonRpc;

use BetaKiller\Auth\AccessDeniedException;
use BetaKiller\Exception\HttpExceptionInterface;
use BetaKiller\Helper\LoggerHelperTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use ReflectionMethod;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\JsonRpc\Exception\HttpJsonRpcException;
use Spotman\Api\JsonRpc\Exception\InternalErrorJsonRpcException;
use Spotman\Api\JsonRpc\Exception\InvalidParamsJsonRpcException;
use Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException;
use Spotman\Api\JsonRpc\Exception\MethodNotFoundJsonRpcException;
use Throwable;

final class JsonRpcServer implements RequestHandlerInterface
{
    use LoggerHelperTrait;

    /**
     * @var callable
     */
    private $proxyFactoryCallable;

    /**
     * @var string[]
     */
    private $accessViolationExceptions;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(ResponseFactoryInterface $responseFactory, LoggerInterface $logger)
    {
        $this->responseFactory = $responseFactory;

        $this->registerProxyFactory([$this, 'defaultProxyFactory']);
        $this->logger = $logger;
    }

    public function registerProxyFactory(callable $factory)
    {
        $this->proxyFactoryCallable = $factory;

        return $this;
    }

    public function addAccessViolationException(string $className)
    {
        $this->accessViolationExceptions[] = $className;

        return $this;
    }

    public function defaultProxyFactory(string $className)
    {
        if (!class_exists($className)) {
            throw new MethodNotFoundJsonRpcException;
        }

        return new $className;
    }

    public function handle(ServerRequestInterface $httpRequest): ResponseInterface
    {
        $response     = null;
        $lastModified = null;

        try {
            $rawBody = $httpRequest->getParsedBody();

            if (!$rawBody) {
                throw new InvalidRequestJsonRpcException;
            }

            if (\is_array($rawBody)) {
                $batchData    = $this->processBatch($rawBody);
                $batchResults = [];

                // Update last modified for each item
                foreach ($batchData as $item) {
                    $lastModified   = $this->updateLastModified($lastModified, $item->getLastModified());
                    $batchResults[] = $item->body();
                }

                $rpcResponse = '['.implode(',', array_filter($batchResults)).']';
            } else {
                $request      = new JsonRpcServerRequest($rawBody);
                $data         = $this->processRequest($request);
                $lastModified = $this->updateLastModified($lastModified, $data->getLastModified());
                $rpcResponse  = $data->body();
            }
        } catch (\Throwable $e) {
            $this->processException($e);

            $e = $this->wrapException($e);

            $rpcResponse = ServerResponse::factory()->failed($e)->body();
        }

        // Send response
        return $this->makeResponse($rpcResponse, $lastModified);
    }

    private function isAccessViolationException(\Throwable $e): bool
    {
        foreach ($this->accessViolationExceptions as $violationException) {
            if ($e instanceof $violationException) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $batchRequest
     *
     * @return \Spotman\Api\JsonRpc\ServerResponse[]
     * @throws \Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException
     * @throws \Spotman\Api\JsonRpc\JsonRpcException
     */
    private function processBatch(array $batchRequest): array
    {
        $results = [];

        // Process each request
        foreach ($batchRequest as $subRequest) {
            $request   = new JsonRpcServerRequest($subRequest);
            $results[] = $this->processRequest($request);
        }

        return array_filter($results);
    }

    /**
     * @param JsonRpcServerRequest $request
     *
     * @return \Spotman\Api\JsonRpc\ServerResponse
     */
    private function processRequest(JsonRpcServerRequest $request): ServerResponse
    {
        // Make response
        $response = ServerResponse::factory()->setId($request->getId());

        $lastModified = new DateTimeImmutable;

        try {
            // Get class/method names
            $resourceName = $request->getResourceName();
            $methodName   = $request->getMethodName();

            // Factory proxy object
            $proxyObject = $this->proxyFactory($resourceName);

            $params = $this->prepareParams($proxyObject, $methodName, $request->getParams() ?: []);

            // Call proxy object method
            $result = \call_user_func_array([$proxyObject, $methodName], $params);

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

    private function proxyFactory(string $className)
    {
        return \call_user_func($this->proxyFactoryCallable, $className);
    }

    private function prepareParams($proxyObject, string $methodName, array $args): array
    {
        if (!$args) {
            return $args;
        }

        // Thru indexed params
        if (\is_int(key($args))) {
            return $args;
        }

        $reflection = new ReflectionMethod($proxyObject, $methodName);

        $params = [];

        foreach ($reflection->getParameters() as $param) {
            if (isset($args[$param->getName()])) {
                $params[] = $args[$param->getName()];
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new InvalidParamsJsonRpcException;
            }
        }

        return $params;
    }

    private function updateLastModified(?DateTimeImmutable $current, ?DateTimeImmutable $updated): DateTimeImmutable
    {
        if (!$current || ($updated && $updated > $current)) {
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

    private function makeResponse(string $rpcResponse, DateTimeInterface $lastModified = null): ResponseInterface
    {
        if (!$lastModified) {
            $lastModified = new DateTime;
        }

        $value = gmdate("D, d M Y H:i:s \G\M\T", $lastModified->getTimestamp());

        $response = $this->responseFactory->createResponse();

        $response->getBody()->write($rpcResponse);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Last-Modified', $value);
    }
}

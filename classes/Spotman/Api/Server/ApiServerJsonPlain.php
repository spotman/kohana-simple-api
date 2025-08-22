<?php

namespace Spotman\Api\Server;

use BetaKiller\Auth\AccessDeniedException;
use BetaKiller\Exception\BadRequestHttpException;
use BetaKiller\Exception\HttpExceptionInterface;
use BetaKiller\Exception\ServerErrorHttpException;
use BetaKiller\Factory\FactoryException;
use BetaKiller\Helper\LoggerHelper;
use BetaKiller\Helper\ResponseHelper;
use BetaKiller\Helper\ServerRequestHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Spotman\Api\ApiAccessViolationException;
use Spotman\Api\ApiFacade;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiServerInterface;
use Throwable;

final readonly class ApiServerJsonPlain extends ApiServerAbstract
{
    public function __construct(private ApiFacade $api, private LoggerInterface $logger)
    {
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
        $user = ServerRequestHelper::getUser($request);

        try {
            $cmd  = $request->getQueryParams()['cmd'] ?? null;
            $body = (array)$request->getParsedBody();

            if (!$cmd) {
                throw new BadRequestHttpException('Missing command argument');
            }

            [$resourceName, $methodName] = explode('.', $cmd, 2);

            if (!$resourceName) {
                throw new BadRequestHttpException('Missing Resource name');
            }

            if (!$methodName) {
                throw new BadRequestHttpException('Missing method name');
            }

            // TODO Deal with version
            $version = (int)$request->getAttribute(ApiServerInterface::API_VERSION_REQUEST_ATTR);

            $rpcResponse = $this->api->getProxy()->call($resourceName, $methodName, $body, $user);

            // Send response
            return $this->makeSuccessResponse($rpcResponse);
        } catch (Throwable $e) {
            LoggerHelper::logUserException($this->logger, $e, $user);

            return $this->makeErrorResponse($e);
        }
    }

    private function makeSuccessResponse(ApiMethodResponse $apiResponse): ResponseInterface
    {
        $response = ResponseHelper::customJson($apiResponse->getStatus(), $apiResponse->getData());
        $response = ResponseHelper::setLastModified($response, $apiResponse->getLastModified());
        $response = ResponseHelper::disableCaching($response);

        return $response;
    }

    private function makeErrorResponse(Throwable $e): ResponseInterface
    {
        $e = $this->wrapException($e);

        $response = ResponseHelper::errorJson($e->getMessage(), $e->getCode());
        $response = ResponseHelper::disableCaching($response);

        return $response;
    }

    private function wrapException(Throwable $e): Throwable
    {
        if ($e instanceof FactoryException) {
            // No method available, throw 404
            return new ServerErrorHttpException('Resource or method not found');
        }

        if ($e instanceof ApiAccessViolationException) {
            // Access violation, throw 403
            return new AccessDeniedException();
        }

        if ($e instanceof HttpExceptionInterface) {
            // Common HTTP exception (transfers HTTP code to response)
            return $e;
        }

        // Wrap unknown exception into InternalError
        return new ServerErrorHttpException();
    }
}

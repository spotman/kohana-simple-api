<?php namespace Spotman\Api\JsonRpc\Exception;

use BetaKiller\Exception\HttpExceptionInterface;
use Spotman\Api\JsonRpc\JsonRpcException;

class HttpJsonRpcException extends JsonRpcException
{
    public function __construct($message, HttpExceptionInterface $exception)
    {
        $this->code = $exception->getCode();

        parent::__construct($message, null, $exception);
    }
}

<?php
namespace Spotman\Api\JsonRpc\Exception;

use Spotman\Api\JsonRpc\JsonRpcException;

class MethodNotFoundJsonRpcException extends JsonRpcException
{
    protected $code = self::METHOD_NOT_FOUND;
}

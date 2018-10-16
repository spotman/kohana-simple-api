<?php
namespace Spotman\Api\JsonRpc\Exception;

use Spotman\Api\JsonRpc\JsonRpcException;

class InvalidRequestJsonRpcException extends JsonRpcException
{
    protected $code = self::INVALID_REQUEST;
}

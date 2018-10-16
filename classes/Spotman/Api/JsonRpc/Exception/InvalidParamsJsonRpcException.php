<?php
namespace Spotman\Api\JsonRpc\Exception;

use Spotman\Api\JsonRpc\JsonRpcException;

class InvalidParamsJsonRpcException extends JsonRpcException
{
    protected $code = self::INVALID_PARAMS;
}

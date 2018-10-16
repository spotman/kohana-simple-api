<?php
namespace Spotman\Api\JsonRpc\Exception;

use Spotman\Api\JsonRpc\JsonRpcException;

class InternalErrorJsonRpcException extends JsonRpcException
{
    protected $code = self::INTERNAL_ERROR;
}

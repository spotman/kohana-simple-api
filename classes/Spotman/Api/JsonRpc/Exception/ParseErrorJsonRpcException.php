<?php
namespace Spotman\Api\JsonRpc\Exception;

use Spotman\Api\JsonRpc\JsonRpcException;

class ParseErrorJsonRpcException extends JsonRpcException
{
    protected $code = self::PARSE_ERROR;
}

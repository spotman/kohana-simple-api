<?php
namespace Spotman\Api\JsonRpc;

use Throwable;

class JsonRpcException extends \BetaKiller\Exception
{
    public const PARSE_ERROR      = -32700;
    public const INVALID_REQUEST  = -32600;
    public const METHOD_NOT_FOUND = -32601;
    public const INVALID_PARAMS   = -32602;
    public const INTERNAL_ERROR   = -32603;

//    const AUTHORIZATION_REQUIRED = -32603;

    private const MESSAGES = [
        self::PARSE_ERROR      => 'Parse error',
        self::INVALID_REQUEST  => 'Invalid Request',
        self::METHOD_NOT_FOUND => 'Method not found',
        self::INVALID_PARAMS   => 'Invalid params',
        self::INTERNAL_ERROR   => 'Internal error',
//        self::AUTHORIZATION_REQUIRED    => 'Authorization required',
    ];

    public function __construct($message = null, $variables = null, Throwable $original_exception = null)
    {
        $code = $this->code;

        parent::__construct($message ?: self::MESSAGES[$code], $variables, $code, $original_exception);
    }
}

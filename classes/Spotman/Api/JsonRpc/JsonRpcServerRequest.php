<?php
declare(strict_types=1);

namespace Spotman\Api\JsonRpc;

use Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException;

final class JsonRpcServerRequest
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $resourceName;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * JsonRpcServerRequest constructor.
     *
     * @param object $body
     */
    public function __construct($body)
    {
        if (!$body || !\is_object($body)) {
            throw new InvalidRequestJsonRpcException;
        }

        // Check protocol version
        if (!isset($body->jsonrpc) || $body->jsonrpc !== '2.0') {
            throw new InvalidRequestJsonRpcException;
        }

        $this->id = isset($body->id) ? (int)$body->id : null;

        $this->params = isset($body->params) ? (array)$body->params : [];

        $rawMethod = isset($body->method) ? (string)$body->method : null;

        if (!$rawMethod) {
            throw new InvalidRequestJsonRpcException;
        }

        $rawMethodArray = explode('.', $rawMethod);

        if (\count($rawMethodArray) !== 2) {
            throw new InvalidRequestJsonRpcException;
        }

        $this->resourceName = $rawMethodArray[0];
        $this->methodName   = $rawMethodArray[1];

        if (!$this->resourceName || !$this->methodName) {
            throw new InvalidRequestJsonRpcException;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}

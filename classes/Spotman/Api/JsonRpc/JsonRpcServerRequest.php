<?php
declare(strict_types=1);

namespace Spotman\Api\JsonRpc;

use Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException;

use function count;

final class JsonRpcServerRequest
{
    /**
     * @var int|null
     */
    private int|null $id;

    /**
     * @var string
     */
    private string $resourceName;

    /**
     * @var string
     */
    private string $methodName;

    /**
     * @var array
     */
    private array $params;

    /**
     * JsonRpcServerRequest constructor.
     *
     * @param array $body
     *
     * @throws \Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException
     */
    public function __construct(array $body)
    {
        if (!$body) {
            throw new InvalidRequestJsonRpcException();
        }

        // Check protocol version
        if (!isset($body['jsonrpc']) || $body['jsonrpc'] !== '2.0') {
            throw new InvalidRequestJsonRpcException();
        }

        $this->id = isset($body['id']) ? (int)$body['id'] : null;

        $this->params = isset($body['params']) ? (array)$body['params'] : [];

        $rawMethod = isset($body['method']) ? (string)$body['method'] : null;

        if (!$rawMethod) {
            throw new InvalidRequestJsonRpcException('Missing method name');
        }

        $rawMethodArray = explode('.', $rawMethod);

        if (count($rawMethodArray) !== 2) {
            throw new InvalidRequestJsonRpcException('Wrong method format, use "resource.method"');
        }

        $this->resourceName = $rawMethodArray[0];
        $this->methodName   = $rawMethodArray[1];

        if (!$this->resourceName) {
            throw new InvalidRequestJsonRpcException('Missing resource name');
        }

        if (!$this->methodName) {
            throw new InvalidRequestJsonRpcException('Missing method name');
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

<?php
namespace Spotman\Api\JsonRpc;

use DateTimeImmutable;
use GuzzleHttp\Client;
use Spotman\Api\JsonRpc\Exception\InvalidRequestJsonRpcException;
use stdClass;

final class JsonRpcClient
{
    private static $lastID = 0;

    public static function factory()
    {
        return new static;
    }

    public function call(string $url, string $method, array $params = null): JsonRpcClientResponse
    {
        $payload = new stdClass;

        $payload->jsonrpc = '2.0';
        $payload->id      = $this->generateId();
        $payload->method  = $method;

        if ($params) {
            $payload->params = $params;
        }

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = $client->request('POST', $url, [
            'body' => \json_encode($payload),
        ]);

        $status = $response->getStatusCode();

        if ($status !== 200) {
            throw new JsonRpcException('Response with :status status from :url -> :method', [
                ':status' => $status,
                ':url'    => $url,
                ':method' => $method,
            ]);
        }

        $data = json_decode($response->getBody()->getContents(), false);

        if (!isset($data->id) || (int)$data->id !== (int)$payload->id) {
            throw new InvalidRequestJsonRpcException('Incorrect response from :url -> :method', [
                ':url'    => $url,
                ':method' => $method,
            ]);
        }

        if (isset($data->error)) {
            throw new JsonRpcException($data->error);
        }

        if (!isset($data->result)) {
            throw new InvalidRequestJsonRpcException();
        }

        $string = $response->getHeaderLine('Last-Modified');
        $format = 'D, d M Y H:i:s \G\M\T';

        $lastModified = DateTimeImmutable::createFromFormat($format, $string);

        return new JsonRpcClientResponse($data->result, $lastModified ?: new DateTimeImmutable);
    }

    private function generateId(): int
    {
        return self::$lastID++;
    }
}

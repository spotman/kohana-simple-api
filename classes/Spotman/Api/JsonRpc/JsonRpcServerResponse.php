<?php
namespace Spotman\Api\JsonRpc;

use DateTimeImmutable;
use stdClass;

final class JsonRpcServerResponse
{
    /**
     * @var int
     */
    private $id;

    private $result;

    /**
     * @var \DateTimeImmutable|null
     */
    private $lastModified;

    /**
     * @var JsonRpcException
     */
    private $error;

    public static function factory()
    {
        return new static;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function succeeded($result)
    {
        $this->result = $result;

        return $this;
    }

    public function failed(JsonRpcException $error)
    {
        $this->error = $error;

        return $this;
    }

    public function setLastModified(DateTimeImmutable $time)
    {
        $this->lastModified = $time;

        return $this;
    }

    public function getLastModified(): ?DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function body()
    {
        $response          = new stdClass;
        $response->jsonrpc = '2.0';
        $response->id      = $this->id;

        // There is a error
        if ($this->error) {
            $error = new stdClass;

            $error->code    = $this->error->getCode();
            $error->message = (string)$this->error->getMessage();

            $response->error = $error;
        } // Notifications does not need response
        elseif (!$this->id) {
            return '';
        } else {
            $response->result = $this->result;
        }

        // Force empty arrays to be empty objects
        return json_encode($response);
    }
}

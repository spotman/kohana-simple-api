<?php
declare(strict_types=1);

namespace Spotman\Api\JsonRpc;

final class JsonRpcClientResponse
{
    /**
     * @var \DateTimeImmutable
     */
    private $lastModified;

    /**
     * @var mixed
     */
    private $data;

    /**
     * JsonRpcClientResponse constructor.
     *
     * @param \DateTimeImmutable $lastModified
     * @param mixed              $data
     */
    public function __construct($data, \DateTimeImmutable $lastModified = null)
    {
        $this->data         = $data;
        $this->lastModified = $lastModified ?? new \DateTimeImmutable;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLastModified(): \DateTimeImmutable
    {
        return $this->lastModified;
    }
}

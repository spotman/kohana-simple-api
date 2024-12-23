<?php

namespace Spotman\Api;

use DateTimeImmutable;
use JsonSerializable;

final readonly class ApiMethodResponse implements JsonSerializable
{
    private const STATUS_OK    = 'ok';
    private const STATUS_ERROR = 'error';

    public static function ok(mixed $data = null, DateTimeImmutable $lastModified = null): self
    {
        return new self($data, $lastModified, self::STATUS_OK);
    }

    public static function error(mixed $data = null, DateTimeImmutable $lastModified = null): self
    {
        return new self($data, $lastModified, self::STATUS_ERROR);
    }

    public static function custom(mixed $data = null, DateTimeImmutable $lastModified = null, ?string $status = null): ApiMethodResponse
    {
        return new self($data, $lastModified, $status);
    }

    /**
     * ApiMethodResponse constructor.
     *
     * @param null                    $data         Result data
     * @param \DateTimeImmutable|null $lastModified Timestamp of the last change
     * @param string|null             $status       Result status
     */
    private function __construct(
        private mixed $data = null,
        private ?DateTimeImmutable $lastModified = null,
        private ?string $status = null
    ) {
    }

    public function getStatus(): string
    {
        return $this->status ?? self::STATUS_OK;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLastModified(): DateTimeImmutable
    {
        return $this->lastModified ?? new DateTimeImmutable();
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
    {
        return $this->asArray();
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            'data'          => $this->getData(),
            'last_modified' => $this->getLastModified()->getTimestamp(),
        ];
    }
}

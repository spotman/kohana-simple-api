<?php
namespace Spotman\Api;

use JsonSerializable;

final class ApiMethodResponse implements JsonSerializable
{
    private $data;

    /**
     * @var \DateTimeImmutable Timestamp of the last change
     */
    private $lastModified;

    /**
     * @param mixed|null         $data
     * @param \DateTimeImmutable $lastModified
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    public static function factory($data = null, \DateTimeImmutable $lastModified = null): ApiMethodResponse
    {
        return new self($data, $lastModified);
    }

    /**
     * ApiMethodResponse constructor.
     *
     * @param                    $data
     * @param \DateTimeImmutable $lastModified
     */
    public function __construct($data = null, \DateTimeImmutable $lastModified = null)
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

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
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

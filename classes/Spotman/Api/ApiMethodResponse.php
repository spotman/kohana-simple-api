<?php
namespace Spotman\Api;

use Traversable;

final class ApiMethodResponse
{
    private $data;

    /**
     * @var \DateTimeImmutable Timestamp of the last change
     */
    private $lastModified;

    private static $allowedResultTypes = [
        'null',
        'boolean',
        'string',
        'integer',
        'double',
    ];

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
     * @param array $input
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiMethodException
     * @throws \Spotman\Api\ApiResponseException
     */
    public static function fromArray(array $input): ApiMethodResponse
    {
        $data = $input['data'] ?? null;

        $timestamp = $input['last_modified'] ?? null;

        if (!$timestamp) {
            throw new ApiResponseException('Last modified time is missing');
        }

        $lastModified = (new \DateTimeImmutable)->setTimestamp($timestamp);

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
        // Cleanup data and cast it to array structures and scalar types
        $this->data         = $this->convertResult($data);
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
     * @return array
     */
    public function asArray(): array
    {
        return [
            'data'          => $this->getData(),
            'last_modified' => $this->getLastModified()->getTimestamp(),
        ];
    }

    private function processLastModified(\DateTimeImmutable $newLastModified): void
    {
        if ($this->lastModified && $newLastModified > $this->lastModified) {
            $this->lastModified = $newLastModified;
        }
    }

    /**
     * @param mixed $modelCallResult
     *
     * @return array|int|string|bool|double|null
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResult($modelCallResult)
    {
        if (\is_object($modelCallResult)) {
            return $this->convertResultObject($modelCallResult);
        }

        if (\is_array($modelCallResult)) {
            return $this->convertResultTraversable($modelCallResult);
        }

        return $this->convertResultSimple($modelCallResult);
    }

    /**
     * @param $object
     *
     * @return int|string|array
     * @throws ApiMethodException
     */
    private function convertResultObject($object)
    {
        if ($object instanceof \JsonSerializable) {
            return $object->jsonSerialize();
        }

        if ($object instanceof ApiResponseItemInterface) {
            // Get item`s last modified time for setting it in current response
            $lastModified = $object->getApiLastModified();

            if ($lastModified) {
                $this->processLastModified($lastModified);
            }

            return $object->getApiResponseData();
        }

        if ($object instanceof Traversable) {
            return $this->convertResultTraversable($object);
        }

        throw new ApiMethodException(
            'API model method may return objects implementing Traversable or ApiModelResponseItemInterface only'
        );
    }

    /**
     * @param array|\Traversable $traversable
     *
     * @return array
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResultTraversable($traversable): array
    {
        $data = [];

        foreach ($traversable as $key => $value) {
            $data[$key] = $this->convertResult($value);
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @return mixed
     * @throws \Spotman\Api\ApiMethodException
     */
    private function convertResultSimple($data)
    {
        $type = \gettype($data);

        if (!\in_array(strtolower($type), static::$allowedResultTypes, true)) {
            throw new ApiMethodException('API model must not return values of type :type', [':type' => $type]);
        }

        return $data;
    }
}

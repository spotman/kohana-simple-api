<?php
namespace Spotman\Api;

use DateTime;
use Traversable;

class ApiMethodResponse implements \JSONRPC_ModelResponseInterface
{
    protected $data;

    /**
     * @var DateTime Timestamp of the last change
     */
    protected $lastModified;

    protected static $allowedResultTypes = [
        'null',
        'boolean',
        'string',
        'integer',
        'double',
    ];

    /**
     * @param null                    $data
     * @param \DateTimeInterface|null $lastModified
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    public static function factory($data = null, \DateTimeInterface $lastModified = null): ApiMethodResponse
    {
        $obj = new static;

        $obj->setData($data);

        if ($lastModified) {
            $obj->setLastModified($lastModified);
        }

        return $obj;
    }

    /**
     * @param mixed $data
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiMethodException
     */
    public function setData($data): ApiMethodResponse
    {
        // Cleanup data and cast it to array structures and scalar types
        $this->data = $this->convertResult($data);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTimeInterface $lastModified
     *
     * @return $this
     */
    public function setLastModified(\DateTimeInterface $lastModified): ApiMethodResponse
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getLastModified(): \DateTimeInterface
    {
        return $this->lastModified ?: new \DateTimeImmutable;
    }

    /**
     * @param array $input
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiMethodException
     * @throws \Spotman\Api\ApiResponseException
     */
    public function fromArray(array $input): ApiMethodResponse
    {
        $data = $input['data'] ?? null;

        $lastModifiedTimestamp = $input['last_modified'] ?? null;

        if (!$lastModifiedTimestamp) {
            throw new ApiResponseException('Last modified time is missing');
        }

        $lastModifiedObject = (new \DateTimeImmutable)->setTimestamp($lastModifiedTimestamp);

        return $this
            ->setData($data)
            ->setLastModified($lastModifiedObject);
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

    /**
     * @return mixed
     */
    public function getJsonRpcResponseData()
    {
        return $this->getData();
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getJsonRpcResponseLastModified(): ?\DateTimeInterface
    {
        return $this->getLastModified();
    }

    protected function processLastModified(\DateTimeInterface $newLastModified): void
    {
        if ($this->lastModified && $newLastModified > $this->lastModified) {
            $this->lastModified = $newLastModified;
        }
    }

    /**
     * @param $modelCallResult
     *
     * @return array|int|string|bool|double|null
     * @throws \Spotman\Api\ApiMethodException
     */
    protected function convertResult($modelCallResult)
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
    protected function convertResultObject($object)
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
    protected function convertResultTraversable($traversable): array
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
    protected function convertResultSimple($data)
    {
        $type = \gettype($data);

        if (!\in_array(strtolower($type), static::$allowedResultTypes, true)) {
            throw new ApiMethodException('API model must not return values of type :type', [':type' => $type]);
        }

        return $data;
    }
}

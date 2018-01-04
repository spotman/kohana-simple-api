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
    public static function factory($data = null, \DateTimeInterface $lastModified = null)
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
     */
    public function setData($data)
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
    public function setLastModified(\DateTimeInterface $lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastModified()
    {
        return $this->lastModified ?: new \DateTimeImmutable;
    }

    public function fromArray(array $input)
    {
        $data = isset($input['data'])
            ? $input['data']
            : null;

        $lastModifiedTimestamp = isset($input['last_modified'])
            ? $input['last_modified']
            : null;

//        if ( ! $data )
//            throw new ApiResponseException('Data is missing');

        if (!$lastModifiedTimestamp) {
            throw new ApiResponseException('Last modified time is missing');
        }

        $lastModifiedObject = (new \DateTimeImmutable)->setTimestamp($lastModifiedTimestamp);

        return $this
            ->setData($data)
            ->setLastModified($lastModifiedObject);
    }

    public function asArray()
    {
        $lastModified = $this->getLastModified() ?: new \DateTimeImmutable;

        return [
            'data'          => $this->getData(),
            'last_modified' => $lastModified->getTimestamp(),
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
    public function getJsonRpcResponseLastModified()
    {
        return $this->getLastModified();
    }

    protected function processLastModified(\DateTimeInterface $newLastModified)
    {
        if ($this->lastModified && $newLastModified > $this->lastModified) {
            $this->lastModified = $newLastModified;
        }
    }

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
     * @returns int|string|array
     * @throws ApiMethodException
     */
    protected function convertResultObject($object)
    {
        if ($object instanceof ApiResponseItemInterface) {
            // Get item`s last modified time for setting it in current response
            $last_modified = $object->getApiLastModified();

            if ($last_modified) {
                $this->processLastModified($last_modified);
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
     */
    protected function convertResultTraversable($traversable)
    {
        $data = [];

        foreach ($traversable as $key => $value) {
            $data[$key] = $this->convertResult($value);
        }

        return $data;
    }

    protected function convertResultSimple($data)
    {
        $type = \gettype($data);

        if (!\in_array(strtolower($type), static::$allowedResultTypes, true)) {
            throw new ApiMethodException('API model must not return values of type :type', [':type' => $type]);
        }

        return $data;
    }
}

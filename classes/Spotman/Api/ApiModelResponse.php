<?php
namespace Spotman\Api;

use DateTime;
use Traversable;

class ApiModelResponse implements \JSONRPC_ModelResponseInterface
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

    public static function factory($data = NULL, DateTime $lastModified = NULL)
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
     * @return \Spotman\Api\ApiModelResponse
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
     * @param DateTime $lastModified
     *
     * @return $this
     */
    public function setLastModified(DateTime $lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * @return NULL|DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified ?: new DateTime;
    }

    public function fromArray(array $input)
    {
        $data = isset($input['data'])
            ? $input['data']
            : NULL;

        $lastModifiedTimestamp = isset($input['last_modified'])
            ? $input['last_modified']
            : NULL;

//        if ( ! $data )
//            throw new ApiResponseException('Data is missing');

        if (!$lastModifiedTimestamp) {
            throw new ApiResponseException('Last modified time is missing');
        }

        $lastModifiedObject = (new DateTime())->setTimestamp($lastModifiedTimestamp);

        return $this
            ->setData($data)
            ->setLastModified($lastModifiedObject);
    }

    public function asArray()
    {
        $lastModified = $this->getLastModified() ?: new DateTime;

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
     * @return \DateTime|null
     */
    public function getJsonRpcResponseLastModified()
    {
        return $this->getLastModified();
    }

    protected function processLastModified(DateTime $newLastModified)
    {
        if ($this->lastModified && $newLastModified > $this->lastModified) {
            $this->lastModified = $newLastModified;
        }
    }

    protected function convertResult($modelCallResult)
    {
        if (is_object($modelCallResult)) {
            return $this->convertResultObject($modelCallResult);
        }

        if (is_array($modelCallResult)) {
            return $this->convertResultTraversable($modelCallResult);
        }

        return $this->convertResultSimple($modelCallResult);
    }

    /**
     * @param $object
     *
     * @returns int|string|array
     * @throws ApiModelException
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

        throw new ApiModelException(
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
        $type = gettype($data);

        if (!in_array(strtolower($type), static::$allowedResultTypes, TRUE)) {
            throw new ApiModelException('API model must not return values of type :type', [':type' => $type]);
        }

        return $data;
    }
}

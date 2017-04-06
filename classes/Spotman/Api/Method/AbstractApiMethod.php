<?php
namespace Spotman\Api\Method;

use Spotman\Api\AccessResolver\AclApiMethodAccessResolver;
use Spotman\Api\ApiMethodInterface;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiResourceInterface;

abstract class AbstractApiMethod implements ApiMethodInterface
{
    /**
     * @var string
     */
    private $methodName;

    /**
     * @var string
     */
    private $collectionName;

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->methodName) {
            $this->parseClassName();
        }

        return $this->methodName;
    }

    private function parseClassName()
    {
        $className = static::class;
        $parts     = explode('\\', $className);
        $baseName  = array_pop($parts);

        // Methods are in camelCase notation
        $this->methodName = lcfirst(str_replace(ApiMethodInterface::SUFFIX, '', $baseName));

        // Lastly placed namespace is collection name
        $collectionName = array_pop($parts);

        $this->collectionName = str_replace(ApiResourceInterface::SUFFIX, '', $collectionName);
    }

    /**
     * @return string
     */
    public function getCollectionName()
    {
        if (!$this->collectionName) {
            $this->parseClassName();
        }

        return $this->collectionName;
    }

    /**
     * @return string
     */
    public function getAccessResolverName()
    {
        return AclApiMethodAccessResolver::CODENAME;
    }

    /**
     * @param mixed|null $data
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    protected function response($data = null)
    {
        return ApiMethodResponse::factory($data);
    }
}

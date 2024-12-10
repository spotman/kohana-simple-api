<?php

namespace Spotman\Api\Method;

use Spotman\Api\AccessResolver\AclApiMethodAccessResolver;
use Spotman\Api\ApiMethodInterface;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiResourceInterface;

abstract readonly class AbstractApiMethod implements ApiMethodInterface
{
    /**
     * @return string
     */
    public static function getCollectionName(): string
    {
        return static::parseClassName()[0];
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return static::parseClassName()[1];
    }

    private static function parseClassName(): array
    {
        static $methodName;
        static $collectionName;

        if (!$collectionName || !$methodName) {
            $className = static::class;
            $parts     = explode('\\', $className);
            $baseName  = array_pop($parts);

            // Methods are in camelCase notation
            $methodName = lcfirst(str_replace(ApiMethodInterface::SUFFIX, '', $baseName));

            // Lastly placed namespace is collection name
            $collectionName = array_pop($parts);

            $collectionName = str_replace(ApiResourceInterface::SUFFIX, '', $collectionName);
        }

        return [
            $collectionName,
            $methodName
        ];
    }

    /**
     * @return string
     */
    public function getAccessResolverName(): string
    {
        return AclApiMethodAccessResolver::CODENAME;
    }

    /**
     * @param mixed|null $data
     *
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    protected function response(mixed $data = null): ?ApiMethodResponse
    {
        return ApiMethodResponse::factory($data);
    }
}

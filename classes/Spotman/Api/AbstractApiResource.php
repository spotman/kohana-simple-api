<?php
namespace Spotman\Api;

abstract class AbstractApiResource implements ApiResourceInterface
{
    public function getName(): string
    {
        $className = static::class;
        $pos       = strrpos($className, '\\');
        $baseName  = substr($className, $pos + 1);

        return str_replace(ApiResourceInterface::SUFFIX, '', $baseName);
    }

    /**
     * Creates API response from raw data (or without it)
     *
     * @param mixed|NULL $data
     *
     * @return ApiMethodResponse
     */
    protected function response($data = null): ApiMethodResponse
    {
        return ApiMethodResponse::factory($data);
    }
}

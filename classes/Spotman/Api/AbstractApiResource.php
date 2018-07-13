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
}

<?php
namespace Spotman\Api;

abstract class ApiModel implements ApiModelInterface
{
    public function getName()
    {
        $className = static::class;
        $pos       = strrpos($className, '\\');
        $baseName  = substr($className, $pos + 1);

        return str_replace('Model', '', $baseName);
    }

    /**
     * Creates API response from raw data (or without it)
     *
     * @param mixed|NULL $data
     *
     * @return ApiModelResponse
     */
    protected function response($data = NULL)
    {
        return ApiModelResponse::factory($data);
    }
}

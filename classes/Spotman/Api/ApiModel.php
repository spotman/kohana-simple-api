<?php
namespace Spotman\Api;

/**
 * Class ApiModel
 * @package Spotman\Api
 * @deprecated Use ApiMethods instead
 */
abstract class ApiModel extends AbstractApiResource implements ApiModelInterface
{
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

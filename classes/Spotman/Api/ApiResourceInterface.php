<?php
namespace Spotman\Api;

interface ApiResourceInterface
{
    public const SUFFIX = 'ApiResource';

    /**
     * @return string
     */
    public function getName(): string;
}

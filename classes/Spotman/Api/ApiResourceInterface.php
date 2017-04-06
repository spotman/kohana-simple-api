<?php
namespace Spotman\Api;

interface ApiResourceInterface
{
    const SUFFIX = 'ApiResource';

    /**
     * @return string
     */
    public function getName();
}

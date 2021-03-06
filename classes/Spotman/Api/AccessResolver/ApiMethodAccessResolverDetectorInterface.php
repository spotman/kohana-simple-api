<?php
namespace Spotman\Api\AccessResolver;

use Spotman\Api\ApiMethodInterface;

interface ApiMethodAccessResolverDetectorInterface
{
    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     *
     * @return string AccessResolver codename
     */
    public function detect(ApiMethodInterface $method): string;
}

<?php
namespace Spotman\Api\AccessResolver;

use Spotman\Api\ApiMethodInterface;

interface ApiMethodAccessResolverInterface
{
    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     *
     * @return bool
     */
    public function isMethodAllowed(ApiMethodInterface $method): bool;
}

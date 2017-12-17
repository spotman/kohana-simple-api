<?php
namespace Spotman\Api\AccessResolver;

use Spotman\Api\ApiMethodInterface;

class DefaultApiMethodAccessResolverDetector implements ApiMethodAccessResolverDetectorInterface
{
    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     *
     * @return string AccessResolver codename
     */
    public function detect(ApiMethodInterface $method): string
    {
        return $method->getAccessResolverName();
    }
}

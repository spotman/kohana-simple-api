<?php
namespace Spotman\Api\AccessResolver;

use BetaKiller\Model\UserInterface;
use Spotman\Api\ApiMethodInterface;

interface ApiMethodAccessResolverInterface
{
    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return bool
     */
    public function isMethodAllowed(ApiMethodInterface $method, UserInterface $user): bool;
}

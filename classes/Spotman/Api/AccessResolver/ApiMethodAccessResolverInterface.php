<?php
namespace Spotman\Api\AccessResolver;

use BetaKiller\Model\UserInterface;
use Spotman\Api\ApiMethodInterface;
use Spotman\Api\ArgumentsInterface;

interface ApiMethodAccessResolverInterface
{
    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     * @param \Spotman\Api\ArgumentsInterface $arguments
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return bool
     */
    public function isMethodAllowed(
        ApiMethodInterface $method,
        ArgumentsInterface $arguments,
        UserInterface $user
    ): bool;
}

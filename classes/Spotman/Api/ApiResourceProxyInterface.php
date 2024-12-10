<?php
namespace Spotman\Api;

use BetaKiller\Model\UserInterface;

interface ApiResourceProxyInterface
{
    public const INTERNAL = 1;
    public const EXTERNAL = 2;

    /**
     * @param string                          $resourceName
     * @param string                          $methodName
     * @param array                           $arguments
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     */
    public function call(string $resourceName, string $methodName, array $arguments, UserInterface $user): ApiMethodResponse;
}

<?php
namespace Spotman\Api;

use BetaKiller\Model\UserInterface;

interface ApiClientInterface
{
    /**
     * @param string                     $resource
     * @param string                     $method
     * @param array                      $arguments
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return ApiMethodResponse
     */
    public function remoteProcedureCall(
        string $resource,
        string $method,
        array $arguments,
        UserInterface $user
    ): ApiMethodResponse;
}

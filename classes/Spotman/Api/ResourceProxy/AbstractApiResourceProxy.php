<?php

namespace Spotman\Api\ResourceProxy;

use BetaKiller\Model\UserInterface;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiResourceProxyException;
use Spotman\Api\ApiResourceProxyInterface;

abstract readonly class AbstractApiResourceProxy implements ApiResourceProxyInterface
{
    /**
     * Simple proxy call to model method
     *
     * @param string                          $resourceName
     * @param string                          $methodName
     * @param array                           $arguments
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     * @throws \Spotman\Api\ApiResourceProxyException
     */
    final public function call(string $resourceName, string $methodName, array $arguments, UserInterface $user): ApiMethodResponse
    {
        $response = $this->callResourceMethod($resourceName, $methodName, $arguments, $user);

        // For methods with empty response
        if ($response === null) {
            $response = ApiMethodResponse::factory();
        }

        if (!($response instanceof ApiMethodResponse)) {
            throw new ApiResourceProxyException('Api model method [:model.:method] must return :must or null', [
                ':model'  => $resourceName,
                ':method' => $methodName,
                ':must'   => ApiMethodResponse::class,
            ]);
        }

        return $response;
    }

    /**
     * @param string                          $resourceName
     * @param string                          $methodName
     * @param array                           $arguments
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    abstract protected function callResourceMethod(
        string $resourceName,
        string $methodName,
        array $arguments,
        UserInterface $user
    ): ?ApiMethodResponse;
}

<?php

namespace Spotman\Api;

use BetaKiller\Model\UserInterface;

abstract readonly class AbstractCallableApiResource extends AbstractApiResource
{
    public function __construct(private ApiFacade $api)
    {
    }

    /**
     * Manually call ApiMethod from MethodsCollection method-helper
     *
     * @param string                          $methodName
     * @param array                           $arguments
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiResourceProxyException
     */
    protected function call(string $methodName, array $arguments, UserInterface $user): ApiMethodResponse
    {
        // Forward call to API subsystem
        return $this->api->getProxy()->call($this->getName(), $methodName, $arguments, $user);
    }
}


<?php
namespace Spotman\Api;

use BetaKiller\Model\UserInterface;

abstract class AbstractCallableApiResource extends AbstractApiResource
{
    /**
     * @var \Spotman\Api\ApiFacade
     */
    protected $api;

    /**
     * AbstractCallableApiResource constructor.
     *
     * @param \Spotman\Api\ApiFacade $api
     */
    public function __construct(ApiFacade $api)
    {
        $this->api = $api;
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
        return $this->api
            ->getResource($this->getName())
            ->call($methodName, $arguments, $user);
    }
}


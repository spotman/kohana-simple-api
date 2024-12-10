<?php

namespace Spotman\Api\ResourceProxy;

use BetaKiller\Model\UserInterface;
use Spotman\Api\ApiClientFactory;
use Spotman\Api\ApiClientInterface;
use Spotman\Api\ApiMethodResponse;

readonly class ExternalApiResourceProxy extends AbstractApiResourceProxy
{
    /**
     * @var \Spotman\Api\ApiClientInterface
     */
    private ApiClientInterface $client;

    /**
     * AbstractApiResourceProxy constructor.
     *
     * @param \Spotman\Api\ApiClientFactory $clientFactory
     *
     * @throws \Spotman\Api\ApiException
     */
    public function __construct(ApiClientFactory $clientFactory)
    {
        $this->client = $clientFactory->createDefault();
    }

    /**
     * @param string                          $resourceName
     * @param string                          $methodName
     * @param array                           $arguments
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    protected function callResourceMethod(string $resourceName, string $methodName, array $arguments, UserInterface $user): ?ApiMethodResponse
    {
        return $this->client->remoteProcedureCall($resourceName, $methodName, $arguments, $user);
    }
}

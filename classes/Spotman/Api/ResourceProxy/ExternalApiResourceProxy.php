<?php
namespace Spotman\Api\ResourceProxy;

use BetaKiller\Model\UserInterface;
use Spotman\Api\ApiClientFactory;
use Spotman\Api\ApiMethodResponse;

class ExternalApiResourceProxy extends AbstractApiResourceProxy
{
    /**
     * @var \Spotman\Api\ApiClientFactory
     */
    private $clientFactory;

    /**
     * AbstractApiResourceProxy constructor.
     *
     * @param string                        $resourceName
     * @param \Spotman\Api\ApiClientFactory $clientFactory
     */
    public function __construct(string $resourceName, ApiClientFactory $clientFactory)
    {
        parent::__construct($resourceName);

        $this->clientFactory = $clientFactory;
    }

    /**
     * @param string                                   $methodName
     * @param array                                    $arguments
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Spotman\Api\ApiException
     */
    protected function callResourceMethod(string $methodName, array $arguments, UserInterface $user): ?ApiMethodResponse
    {
        $client = $this->clientFactory->createDefault();

        return $client->remoteProcedureCall($this->resourceName, $methodName, $arguments, $user);
    }
}

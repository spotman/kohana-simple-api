<?php
namespace Spotman\Api\ResourceProxy;

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
     * Performs remote API call
     *
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     * @throws \Spotman\Api\ApiException
     */
    public function call(string $methodName, array $arguments): ApiMethodResponse
    {
        $client = $this->clientFactory->createDefault();

        return $client->remote_procedure_call($this->resourceName, $methodName, $arguments);
    }
}

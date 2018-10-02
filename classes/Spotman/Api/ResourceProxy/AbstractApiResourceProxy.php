<?php
namespace Spotman\Api\ResourceProxy;

use BetaKiller\Model\UserInterface;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiResourceProxyException;
use Spotman\Api\ApiResourceProxyInterface;

abstract class AbstractApiResourceProxy implements ApiResourceProxyInterface
{
    /**
     * @var string
     */
    protected $resourceName;

    /**
     * AbstractApiResourceProxy constructor.
     *
     * @param string $resourceName
     */
    public function __construct(string $resourceName)
    {
        $this->resourceName = $resourceName;
    }

    /**
     * Simple proxy call to model method
     *
     * @param string                                   $methodName
     * @param array                                    $arguments
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     * @throws \Spotman\Api\ApiResourceProxyException
     */
    final public function call(string $methodName, array $arguments, UserInterface $user): ApiMethodResponse
    {
        $response = $this->callResourceMethod($methodName, $arguments, $user);

        // For methods with empty response
        if ($response === null) {
            $response = ApiMethodResponse::factory();
        }

        if (!($response instanceof ApiMethodResponse)) {
            throw new ApiResourceProxyException('Api model method [:model.:method] must return :must or null', [
                ':model'  => $this->resourceName,
                ':method' => $methodName,
                ':must'   => ApiMethodResponse::class,
            ]);
        }

        return $response;
    }

    /**
     * @param string                          $methodName
     * @param array                           $arguments
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    abstract protected function callResourceMethod(string $methodName, array $arguments, UserInterface $user): ?ApiMethodResponse;
}

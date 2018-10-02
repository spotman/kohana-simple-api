<?php
namespace Spotman\Api\ResourceProxy;

use BetaKiller\Model\UserInterface;
use Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory;
use Spotman\Api\ApiAccessViolationException;
use Spotman\Api\ApiMethodFactory;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiResourceFactory;

class InternalApiResourceProxy extends AbstractApiResourceProxy
{
    /**
     * @var \Spotman\Api\ApiResourceInterface
     */
    protected $resourceInstance;

    /**
     * @var \Spotman\Api\ApiMethodFactory
     */
    protected $methodFactory;

    /**
     * @var \Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory
     */
    protected $accessResolverFactory;

    /**
     * InternalApiResourceProxy constructor.
     *
     * @param string                                                     $resourceName
     * @param \Spotman\Api\ApiResourceFactory                            $resourceFactory
     * @param \Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory $accessResolverFactory
     * @param \Spotman\Api\ApiMethodFactory                              $methodFactory
     *
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function __construct(
        string $resourceName,
        ApiResourceFactory $resourceFactory,
        ApiMethodAccessResolverFactory $accessResolverFactory,
        ApiMethodFactory $methodFactory
    ) {
        parent::__construct($resourceName);

        $this->resourceInstance      = $resourceFactory->create($resourceName);
        $this->accessResolverFactory = $accessResolverFactory;
        $this->methodFactory         = $methodFactory;
    }

    /**
     * @param string                          $methodName
     * @param array                           $arguments
     *
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \BetaKiller\Factory\FactoryException
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    protected function callResourceMethod(string $methodName, array $arguments, UserInterface $user): ?ApiMethodResponse
    {
        $resource = $this->resourceInstance;

        // Creating method instance (inject current user in ApiMethod)
        $methodInstance = $this->methodFactory->createMethod($resource->getName(), $methodName, $arguments, $user);

        // Getting method access resolver
        $resolverInstance = $this->accessResolverFactory->createFromApiMethod($methodInstance);

        // Security check
        if (!$resolverInstance->isMethodAllowed($methodInstance, $user)) {
            throw new ApiAccessViolationException('Access denied to :collection.:method', [
                ':collection' => $resource->getName(),
                ':method'     => $methodName,
            ]);
        }

        return $methodInstance->execute();
    }
}

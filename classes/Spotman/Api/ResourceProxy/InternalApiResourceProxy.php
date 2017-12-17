<?php
namespace Spotman\Api\ResourceProxy;

use Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory;
use Spotman\Api\API;
use Spotman\Api\ApiAccessViolationException;
use Spotman\Api\ApiMethodFactory;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiMethodsCollectionInterface;
use Spotman\Api\ApiModelInterface;
use Spotman\Api\ApiModelProxyException;
use Spotman\Api\ApiModelWithoutPermissionsInterface;
use Spotman\Api\ApiResourceFactory;

class InternalApiResourceProxy extends AbstractApiResourceProxy
{
    /**
     * @var ApiModelInterface|ApiMethodsCollectionInterface
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
     * @throws \Spotman\Api\ApiMethodException
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
     * Simple proxy call to model method
     *
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse Result of the API call
     * @throws \BetaKiller\Factory\FactoryException
     * @throws \Spotman\Api\ApiAccessViolationException
     * @throws \Spotman\Api\ApiModelProxyException
     */
    public function call(string $methodName, array $arguments): ApiMethodResponse
    {
        return $this->callModelMethod($methodName, $arguments);
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \BetaKiller\Factory\FactoryException
     * @throws \Spotman\Api\ApiModelProxyException
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    protected function callModelMethod(string $methodName, array $arguments): ApiMethodResponse
    {
        if ($this->resourceInstance instanceof ApiMethodsCollectionInterface) {
            $response = $this->callMethodsCollectionMethod($this->resourceInstance, $methodName, $arguments);
        } else {
            $response = $this->callApiModelMethod($this->resourceInstance, $methodName, $arguments);
        }

        // For methods with empty response
        if ($response === null) {
            $response = ApiMethodResponse::factory();
        }

        if (!($response instanceof ApiMethodResponse)) {
            throw new ApiModelProxyException('Api model method [:model.:method] must return :must or null', [
                ':model'  => $this->resourceName,
                ':method' => $methodName,
                ':must'   => ApiMethodResponse::class,
            ]);
        }

        return $response;
    }

    /**
     * @param \Spotman\Api\ApiMethodsCollectionInterface $collection
     * @param string                                     $methodName
     * @param array                                      $arguments
     *
     * @return null|\Spotman\Api\ApiMethodResponse
     * @throws \BetaKiller\Factory\FactoryException
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    protected function callMethodsCollectionMethod(
        ApiMethodsCollectionInterface $collection,
        string $methodName,
        array $arguments
    ): ?ApiMethodResponse {
        // Creating method instance
        $methodInstance = $this->methodFactory->createMethod($collection->getName(), $methodName, $arguments);

        // Getting method access resolver
        $resolverInstance = $this->accessResolverFactory->createFromApiMethod($methodInstance);

        // Security check
        if (!$resolverInstance->isMethodAllowed($methodInstance)) {
            throw new ApiAccessViolationException('Access denied to :collection.:method', [
                ':collection' => $collection->getName(),
                ':method'     => $methodName,
            ]);
        }

        return $methodInstance->execute();
    }

    /**
     * @param \Spotman\Api\ApiModelInterface $model
     * @param string                         $methodName
     * @param array                          $arguments
     *
     * @return ApiMethodResponse
     * @throws \Spotman\Api\ApiModelProxyException
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    protected function callApiModelMethod(
        ApiModelInterface $model,
        string $methodName,
        array $arguments
    ): ApiMethodResponse {
        $this->checkModelPermissions();

        if (!\is_callable([$model, $methodName])) {
            throw new ApiModelProxyException('Unknown method :method in proxy object :class', [
                ':method' => $methodName,
                ':class'  => \get_class($model),
            ]);
        }

        $arguments = API::prepareNamedArguments($model, $methodName, $arguments);

        return \call_user_func_array([$model, $methodName], $arguments);
    }

    /**
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    protected function checkModelPermissions()
    {
        // Skip models without permissions
        if ($this->resourceInstance instanceof ApiModelWithoutPermissionsInterface) {
            return;
        }

        throw new ApiAccessViolationException('ApiModel must not have external permissions, use methods collections instead');
    }
}

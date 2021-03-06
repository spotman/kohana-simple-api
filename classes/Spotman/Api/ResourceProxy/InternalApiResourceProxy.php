<?php
namespace Spotman\Api\ResourceProxy;

use BetaKiller\Model\UserInterface;
use InvalidArgumentException;
use Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory;
use Spotman\Api\ApiAccessViolationException;
use Spotman\Api\ApiLanguageDetectorInterface;
use Spotman\Api\ApiMethodException;
use Spotman\Api\ApiMethodFactory;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiMethodResponseConverterInterface;
use Spotman\Api\ApiResourceFactory;
use Spotman\Defence\ArgumentsFacade;
use Spotman\Defence\DefinitionBuilder;

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
     * @var \Spotman\Defence\ArgumentsFacade
     */
    private $argumentsFacade;

    /**
     * @var \Spotman\Api\ApiMethodResponseConverterInterface
     */
    private $converter;

    /**
     * @var \Spotman\Api\ApiLanguageDetectorInterface
     */
    private $langDetector;

    /**
     * InternalApiResourceProxy constructor.
     *
     * @param string                                                     $resourceName
     * @param \Spotman\Api\ApiResourceFactory                            $resourceFactory
     * @param \Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory $accessResolverFactory
     * @param \Spotman\Api\ApiMethodFactory                              $methodFactory
     * @param \Spotman\Defence\ArgumentsFacade                           $argumentsFacade
     * @param \Spotman\Api\ApiLanguageDetectorInterface                  $langDetector
     * @param \Spotman\Api\ApiMethodResponseConverterInterface           $converter
     *
     * @throws \BetaKiller\Factory\FactoryException
     */
    public function __construct(
        string $resourceName,
        ApiResourceFactory $resourceFactory,
        ApiMethodAccessResolverFactory $accessResolverFactory,
        ApiMethodFactory $methodFactory,
        ArgumentsFacade $argumentsFacade,
        ApiLanguageDetectorInterface $langDetector,
        ApiMethodResponseConverterInterface $converter
    ) {
        parent::__construct($resourceName);

        $this->resourceInstance      = $resourceFactory->create($resourceName);
        $this->accessResolverFactory = $accessResolverFactory;
        $this->methodFactory         = $methodFactory;
        $this->argumentsFacade       = $argumentsFacade;
        $this->converter             = $converter;
        $this->langDetector          = $langDetector;
    }

    /**
     * @param string                          $methodName
     * @param array                           $argumentsArray
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \BetaKiller\Factory\FactoryException
     * @throws \Spotman\Api\ApiAccessViolationException
     */
    protected function callResourceMethod(
        string $methodName,
        array $argumentsArray,
        UserInterface $user
    ): ?ApiMethodResponse {
        $resource = $this->resourceInstance;

        // Creating method instance (inject current user in ApiMethod)
        $methodInstance = $this->methodFactory->createMethod($resource->getName(), $methodName);

        $builder = new DefinitionBuilder();

        // Get arguments definition
        $methodInstance->defineArguments($builder);

        try {
            // Prepare arguments from raw data and definition
            $arguments = $this->argumentsFacade->prepareArguments($argumentsArray, $builder);
        } catch (InvalidArgumentException $e) {
            throw new ApiMethodException(':error in API :collection.:method', [
                ':error'      => $e->getMessage(),
                ':collection' => $methodInstance->getCollectionName(),
                ':method'     => $methodInstance->getName(),
            ], 0, $e);
        }

        // Getting method access resolver
        $resolverInstance = $this->accessResolverFactory->createFromApiMethod($methodInstance);

        // Security check
        if (!$resolverInstance->isMethodAllowed($methodInstance, $arguments, $user)) {
            throw new ApiAccessViolationException('Access denied to ":collection.:method:id" for user ":user"', [
                ':collection' => $resource->getName(),
                ':method'     => $methodName,
                ':id'         => $arguments->hasID() ? '('.$arguments->getID().')' : '',
                ':user'       => $user->hasID() ? $user->getID() : 'Guest',
            ]);
        }

        // Detect lang for Entities converter
        $lang = $this->langDetector->detect($methodInstance, $arguments, $user);

        $response = $methodInstance->execute($arguments, $user);

        // Cleanup data and cast it to array structures and scalar types
        return $response
            ? $this->converter->convert($methodInstance, $response, $user, $lang)
            : null;
    }
}

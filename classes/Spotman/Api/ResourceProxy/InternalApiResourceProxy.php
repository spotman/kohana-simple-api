<?php

namespace Spotman\Api\ResourceProxy;

use BetaKiller\Api\Method\EntityBasedApiMethodInterface;
use BetaKiller\Model\UserInterface;
use InvalidArgumentException;
use Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory;
use Spotman\Api\ApiAccessViolationException;
use Spotman\Api\ApiLanguageDetectorInterface;
use Spotman\Api\ApiMethodException;
use Spotman\Api\ApiMethodFactory;
use Spotman\Api\ApiMethodResponse;
use Spotman\Api\ApiMethodResponseConverterInterface;
use Spotman\Api\EntityDetectorInterface;
use Spotman\Defence\ArgumentsFacade;
use Spotman\Defence\DefinitionBuilder;

readonly class InternalApiResourceProxy extends AbstractApiResourceProxy
{
    /**
     * InternalApiResourceProxy constructor.
     *
     * @param \Spotman\Api\AccessResolver\ApiMethodAccessResolverFactory $accessResolverFactory
     * @param \Spotman\Api\ApiMethodFactory                              $methodFactory
     * @param \Spotman\Defence\ArgumentsFacade                           $argumentsFacade
     * @param \Spotman\Api\ApiLanguageDetectorInterface                  $langDetector
     * @param \Spotman\Api\ApiMethodResponseConverterInterface           $converter
     * @param \Spotman\Api\EntityDetectorInterface                       $entityDetector
     */
    public function __construct(
        private ApiMethodAccessResolverFactory $accessResolverFactory,
        private ApiMethodFactory $methodFactory,
        private ArgumentsFacade $argumentsFacade,
        private ApiLanguageDetectorInterface $langDetector,
        private ApiMethodResponseConverterInterface $converter,
        private EntityDetectorInterface $entityDetector
    ) {
    }

    /**
     * @param string                          $resourceName
     * @param string                          $methodName
     * @param array                           $argumentsArray
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse|null
     * @throws \BetaKiller\Factory\FactoryException
     * @throws \Spotman\Api\ApiAccessViolationException
     * @throws \Spotman\Api\ApiMethodException
     */
    protected function callResourceMethod(
        string $resourceName,
        string $methodName,
        array $argumentsArray,
        UserInterface $user
    ): ?ApiMethodResponse {
        // Creating method instance (inject current user in ApiMethod)
        $methodInstance = $this->methodFactory->createMethod($resourceName, $methodName);

        $builder = new DefinitionBuilder();

        // Get arguments definition
        $methodInstance->defineArguments($builder);

        try {
            // Prepare arguments from raw data and definition
            $arguments = $this->argumentsFacade->prepareArguments($argumentsArray, $builder);
        } catch (InvalidArgumentException $e) {
            throw new ApiMethodException(':error in API :collection.:method', [
                ':error'      => $e->getMessage(),
                ':collection' => $methodInstance::getCollectionName(),
                ':method'     => $methodInstance::getName(),
            ], 0, $e);
        }

        // Getting method access resolver
        $resolverInstance = $this->accessResolverFactory->createFromApiMethod($methodInstance);

        // Security check
        if (!$resolverInstance->isMethodAllowed($methodInstance, $arguments, $user)) {
            throw new ApiAccessViolationException('Access denied to ":collection.:method:id" for user ":user"', [
                ':collection' => $resourceName,
                ':method'     => $methodName,
                ':id'         => $arguments->hasID() ? '('.$arguments->getID().')' : '',
                ':user'       => $user->hasID() ? $user->getID() : 'Guest',
            ]);
        }

        // Detect lang for Entities converter
        $lang = $this->langDetector->detect($methodInstance, $arguments, $user);

        $response = $methodInstance->execute($arguments, $user);

        $entity = $methodInstance instanceof EntityBasedApiMethodInterface
            ? $this->entityDetector->getEntity($methodInstance, $arguments)
            : null;

        // Cleanup data and cast it to array structures and scalar types
        return $response
            ? $this->converter->convert($methodInstance, $response, $user, $lang, $entity)
            : null;
    }
}

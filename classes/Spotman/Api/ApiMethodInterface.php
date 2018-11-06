<?php
namespace Spotman\Api;

use BetaKiller\Model\UserInterface;

interface ApiMethodInterface
{
    public const SUFFIX = 'ApiMethod';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getCollectionName(): string;

    /**
     * @return string
     */
    public function getAccessResolverName(): string;

    /**
     * @return \Spotman\Api\ArgumentsDefinitionInterface
     */
    public function getArgumentsDefinition(): ArgumentsDefinitionInterface;

    /**
     * @param \Spotman\Api\ArgumentsInterface $arguments
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute(ArgumentsInterface $arguments, UserInterface $user): ?ApiMethodResponse;
}

<?php
namespace Spotman\Api;

use BetaKiller\Model\UserInterface;
use Spotman\Defence\ArgumentsDefinitionInterface;
use Spotman\Defence\ArgumentsInterface;

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
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function getArgumentsDefinition(): ArgumentsDefinitionInterface;

    /**
     * @param \Spotman\Defence\ArgumentsInterface $arguments
     * @param \BetaKiller\Model\UserInterface     $user
     *
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute(ArgumentsInterface $arguments, UserInterface $user): ?ApiMethodResponse;
}

<?php

namespace Spotman\Api\AccessResolver;

use BetaKiller\Model\UserInterface;
use Spotman\Acl\AclInterface;
use Spotman\Acl\Resource\ResolvingResourceInterface;
use Spotman\Api\ApiMethodException;
use Spotman\Api\ApiMethodInterface;
use Spotman\Defence\ArgumentsInterface;

readonly class AclApiMethodAccessResolver implements ApiMethodAccessResolverInterface
{
    public const CODENAME = 'Acl';

    /**
     * AclApiMethodAccessResolver constructor.
     *
     * @param \Spotman\Acl\AclInterface $acl
     */
    public function __construct(private AclInterface $acl)
    {
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface     $method
     * @param \Spotman\Defence\ArgumentsInterface $arguments
     * @param \BetaKiller\Model\UserInterface     $user
     *
     * @return bool
     * @throws \Spotman\Api\ApiMethodException
     */
    public function isMethodAllowed(
        ApiMethodInterface $method,
        ArgumentsInterface $arguments,
        UserInterface $user
    ): bool {
        $resource = $this->getAclResourceFromApiMethod($method);

        $this->prepareResource($resource, $method, $arguments, $user);

        $aclPermissionName = $method::getName();

        return $resource->isPermissionAllowed($aclPermissionName);
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     *
     * @return ResolvingResourceInterface
     * @throws \Spotman\Api\ApiMethodException
     */
    protected function getAclResourceFromApiMethod(ApiMethodInterface $method): ResolvingResourceInterface
    {
        $aclResourceName = $method::getCollectionName();

        $resource = $this->acl->getResource($aclResourceName);

        if (!($resource instanceof ResolvingResourceInterface)) {
            throw new ApiMethodException('Api method [:method] must provide acl resource implementing :interface', [
                ':method'    => $method::getCollectionName().'.'.$method::getName(),
                ':interface' => ResolvingResourceInterface::class,
            ]);
        }

        return $resource;
    }

    protected function prepareResource(
        ResolvingResourceInterface $resource,
        ApiMethodInterface $method,
        ArgumentsInterface $arguments,
        UserInterface $user
    ): void {
        // Ugly workaround for using unused methods
        if (!$method || !$arguments) {
            throw new \LogicException('Api method and arguments must be defined');
        }

        $this->acl->injectUserResolver($user, $resource);
    }
}

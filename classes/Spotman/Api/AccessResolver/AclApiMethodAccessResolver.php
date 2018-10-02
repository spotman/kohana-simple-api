<?php
namespace Spotman\Api\AccessResolver;

use BetaKiller\Model\UserInterface;
use Spotman\Acl\AclInterface;
use Spotman\Acl\Resource\ResolvingResourceInterface;
use Spotman\Api\ApiMethodException;
use Spotman\Api\ApiMethodInterface;

class AclApiMethodAccessResolver implements ApiMethodAccessResolverInterface
{
    public const CODENAME = 'Acl';

    /**
     * @var \Spotman\Acl\AclInterface
     */
    protected $acl;

    /**
     * AclApiMethodAccessResolver constructor.
     *
     * @param \Spotman\Acl\AclInterface $acl
     */
    public function __construct(AclInterface $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param \Spotman\Api\ApiMethodInterface $method
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return bool
     * @throws \Spotman\Api\ApiMethodException
     */
    public function isMethodAllowed(ApiMethodInterface $method, UserInterface $user): bool
    {
        $resource = $this->getAclResourceFromApiMethod($method);

        $this->acl->injectUserResolver($user, $resource);

        $aclPermissionName = $method->getName();

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
        $aclResourceName = $method->getCollectionName();

        $resource = $this->acl->getResource($aclResourceName);

        if (!($resource instanceof ResolvingResourceInterface)) {
            throw new ApiMethodException('Api method [:method] must provide acl resource implementing :interface', [
                ':method'    => $method->getCollectionName().'.'.$method->getName(),
                ':interface' => ResolvingResourceInterface::class,
            ]);
        }

        return $resource;
    }
}

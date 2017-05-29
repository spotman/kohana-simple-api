<?php
namespace Spotman\Api\AccessResolver;

use Spotman\Acl\AclInterface;
use Spotman\Acl\Resource\ResolvingResourceInterface;
use Spotman\Api\ApiMethodException;
use Spotman\Api\ApiMethodInterface;

class AclApiMethodAccessResolver implements ApiMethodAccessResolverInterface
{
    const CODENAME = 'Acl';

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
     *
     * @return bool
     */
    public function isMethodAllowed(ApiMethodInterface $method)
    {
        $resource = $this->getAclResourceFromApiMethod($method);

        $aclPermissionName = $method->getName();

        return $resource->isPermissionAllowed($aclPermissionName);
    }

    protected function getAclResourceFromApiMethod(ApiMethodInterface $method)
    {
        $aclResourceName = $method->getCollectionName();

        /** @var ResolvingResourceInterface $resource */
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

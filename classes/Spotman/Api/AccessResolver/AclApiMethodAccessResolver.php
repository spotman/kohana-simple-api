<?php
namespace Spotman\Api\AccessResolver;

use Spotman\Acl\AccessResolver\AclAccessResolverInterface;
use Spotman\Acl\Acl;
use Spotman\Acl\Resource\ResolvingResourceInterface;
use Spotman\Api\ApiMethodException;
use Spotman\Api\ApiMethodInterface;

class AclApiMethodAccessResolver implements ApiMethodAccessResolverInterface
{
    const CODENAME = 'Acl';

    /**
     * @var \Spotman\Acl\Acl
     */
    protected $acl;

    /**
     * @var AclAccessResolverInterface
     */
    protected $resolver;

    /**
     * AclApiMethodAccessResolver constructor.
     *
     * @param \Spotman\Acl\Acl                                       $acl
     * @param \Spotman\Acl\AccessResolver\AclAccessResolverInterface $resolver
     */
    public function __construct(Acl $acl, AclAccessResolverInterface $resolver)
    {
        $this->acl      = $acl;
        $this->resolver = $resolver;
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

        $resource->useResolver($this->resolver);

        return $resource;
    }
}

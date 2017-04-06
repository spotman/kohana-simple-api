<?php
namespace Spotman\Api;

interface ApiMethodInterface
{
    const SUFFIX = 'ApiMethod';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getCollectionName();

    /**
     * @return string
     */
    public function getAccessResolverName();

    /**
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute();
}

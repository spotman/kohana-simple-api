<?php
namespace Spotman\Api;

interface ApiMethodInterface
{
    const SUFFIX = 'ApiMethod';

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
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute(): ?ApiMethodResponse;
}

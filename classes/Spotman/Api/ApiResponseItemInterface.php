<?php
namespace Spotman\Api;

interface ApiResponseItemInterface
{
    /**
     * @return array|\Traversable
     */
    public function getApiResponseData();

    /**
     * @return \DateTimeImmutable|null
     */
    public function getApiLastModified(): ?\DateTimeImmutable;
}

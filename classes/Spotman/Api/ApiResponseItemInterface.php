<?php
namespace Spotman\Api;

interface ApiResponseItemInterface
{
    /**
     * @return callable
     */
    public function getApiResponseData(): callable;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getApiLastModified(): ?\DateTimeImmutable;
}

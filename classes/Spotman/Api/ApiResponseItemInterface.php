<?php
namespace Spotman\Api;

interface ApiResponseItemInterface
{
    /**
     * @return array|\Traversable
     */
    public function getApiResponseData();

    /**
     * @return \DateTime|NULL
     */
    public function getApiLastModified();
}

<?php
namespace Spotman\Api;

interface ApiProxyInterface
{
    const EXTERNAL = 2;
    const INTERNAL = 1;

    /**
     * @return \Spotman\Api\ApiModelInterface
     */
    public function getModel();

    /**
     * @param \Spotman\Api\ApiModelInterface $model
     *
     * @deprecated Move this into constructor and modify in factory
     * @return $this
     */
    public function setModel(ApiModelInterface $model);

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiModelResponse
     */
    public function __call($method, $arguments);
}

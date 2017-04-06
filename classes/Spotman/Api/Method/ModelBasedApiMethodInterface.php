<?php
namespace Spotman\Api\Method;

use Spotman\Api\ApiMethodInterface;

interface ModelBasedApiMethodInterface extends ApiMethodInterface
{
    /**
     * @return \Spotman\Api\AbstractCrudMethodsModelInterface
     */
    public function getModel();
}

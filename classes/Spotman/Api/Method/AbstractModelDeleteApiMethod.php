<?php
namespace Spotman\Api\Method;

use Spotman\Api\ApiMethodException;

abstract class AbstractModelDeleteApiMethod extends AbstractModelBasedApiMethod
{
    public function __construct($id)
    {
        $this->id = (int)$id;

        if (!$this->id) {
            throw new ApiMethodException('Can not delete model with empty id');
        }
    }

    /**
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute()
    {
        $model = $this->getModel();

        return $this->response($this->delete($model));
    }

    /**
     * Implement this method
     *
     * @param $model
     *
     * @throws \Spotman\Api\ApiMethodException
     * @return bool
     */
    abstract protected function delete($model);
}

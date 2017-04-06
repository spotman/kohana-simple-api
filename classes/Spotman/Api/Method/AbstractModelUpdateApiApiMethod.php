<?php
namespace Spotman\Api\Method;

use Spotman\Api\ApiMethodException;

abstract class AbstractModelUpdateApiApiMethod extends AbstractModelBasedApiMethod
{
    /**
     * @var \stdClass
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;

        if (!isset($this->data->id) || !$this->data->id) {
            throw new ApiMethodException('Can not update model with empty id');
        }

        $this->id = (int)$this->data->id;
    }

    /**
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute()
    {
        $model = $this->getModel();
        $response_data = $this->update($model, $this->data);

        return $this->response($response_data);
    }

    /**
     * Override this method
     *
     * @param $model
     * @param $data
     *
     * @throws \Spotman\Api\ApiMethodException
     * @return \Spotman\Api\AbstractCrudMethodsModelInterface|null
     */
    abstract protected function update($model, $data);
}

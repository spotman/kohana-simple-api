<?php
namespace Spotman\Api\Method;

/**
 * Class AbstractModelSaveApiMethod
 * @package Spotman\Api\Method
 * @deprecated Use AbstractModelCreateApiMethod and AbstractModelUpdateApiMethod
 */
abstract class AbstractModelSaveApiMethod extends AbstractModelBasedApiMethod
{
    /**
     * @var \stdClass
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;

        $this->id = (isset($this->data->id) && (int)$this->data->id)
            ? (int)$this->data->id
            : null;
    }

    /**
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute()
    {
        $model = $this->getModel();

        $response_data = $this->_save($model, $this->data);

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
    abstract protected function _save($model, $data);
}

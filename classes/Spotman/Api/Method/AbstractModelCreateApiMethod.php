<?php
namespace Spotman\Api\Method;

abstract class AbstractModelCreateApiMethod extends AbstractModelBasedApiMethod
{
    /**
     * @var \stdClass
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Spotman\Api\ApiMethodResponse|null
     */
    public function execute()
    {
        $model = $this->getModel();
        $responseData = $this->create($model, $this->data);

        return $this->response($responseData);
    }

    /**
     * Override this method
     *
     * @param $model
     * @param $data
     *
     * @throws \Spotman\Api\ApiMethodException
     * @return \Spotman\Api\AbstractCrudMethodsModelInterface
     */
    abstract protected function create($model, $data);
}

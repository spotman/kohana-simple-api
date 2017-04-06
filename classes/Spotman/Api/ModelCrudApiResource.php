<?php
namespace Spotman\Api;

/**
 * Class ModelCrudApiResource
 * @package Spotman\Api
 * @deprecated Use ApiMethods instead
 */
abstract class ModelCrudApiResource extends ApiModel
{
    public function one($id)
    {
        return $this->response($this->_one((int)$id));
    }

    /**
     * Override this if needed
     *
     * @param $id
     *
     * @return \Spotman\Api\AbstractCrudMethodsModelInterface
     */
    protected function _one($id)
    {
        return $this->model($id);
    }

    public function save($data)
    {
        $id = (isset($data->id) AND (int)$data->id)
            ? (int)$data->id
            : NULL;

        $model = $this->model($id);

        $response_data = $this->_save($model, $data);

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
    protected function _save($model, $data)
    {
        throw new ApiMethodException('Not implemented');
    }

    public function delete($id)
    {
        $model = $this->model((int)$id);

        return $this->response($this->_delete($model));
    }

    /**
     * Override this if needed
     *
     * @param \Spotman\Api\AbstractCrudMethodsModelInterface $model
     *
     * @throws \Spotman\Api\ApiMethodException
     * @return bool
     */
    protected function _delete($model)
    {
        throw new ApiMethodException('Not implemented');
    }

    /**
     * Returns new model or performs search by id
     *
     * @param int|null $id
     *
     * @return \Spotman\Api\AbstractCrudMethodsModelInterface
     */
    abstract protected function model($id = NULL);
}

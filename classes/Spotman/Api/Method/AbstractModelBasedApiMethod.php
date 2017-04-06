<?php
namespace Spotman\Api\Method;

abstract class AbstractModelBasedApiMethod extends AbstractApiMethod implements ModelBasedApiMethodInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \Model_ContentPost
     */
    protected $model;

    /**
     * Returns new model or performs search by id
     *
     * @param int|null $id
     *
     * @return \Spotman\Api\AbstractCrudMethodsModelInterface
     */
    abstract protected function modelFactory($id = NULL);

    /**
     * @return \Spotman\Api\AbstractCrudMethodsModelInterface
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = $this->modelFactory($this->id);
        }

        return $this->model;
    }
}

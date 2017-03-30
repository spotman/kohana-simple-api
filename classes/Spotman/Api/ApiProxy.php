<?php
namespace Spotman\Api;

use ReflectionMethod;

abstract class ApiProxy implements ApiProxyInterface
{
    protected $_model;

    /**
     * @return \Spotman\Api\ApiModelInterface
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param \Spotman\Api\ApiModelInterface $model
     *
     * @deprecated Move this into constructor and modify in factory
     * @return $this
     */
    public function setModel(ApiModelInterface $model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiModelResponse
     */
    final public function __call($method, $arguments)
    {
        $data = $this->call($method, $arguments);

        return ($data instanceof ApiModelResponse)
            ? $data
            : ApiModelResponse::factory()->fromArray($data);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return \Spotman\Api\ApiModelResponse|array Result of the API call
     */
    abstract protected function call($method, array $arguments);

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return ApiModelResponse
     * @throws ApiProxyException
     * @throws ApiModelException
     */
    protected function callModelMethod($method, array $arguments)
    {
        $model = $this->getModel();

        if (!is_callable([$model, $method])) {
            throw new ApiProxyException('Unknown method :method in proxy object :class',
                [':method' => $method, ':class' => get_class($model)]);
        }

        // TODO deal with missed/unordered arguments

        /** @var ApiModelResponse $result */
        $result = call_user_func_array([$model, $method], $arguments);

        // For model methods without response
        if ($result === null) {
            $result = ApiModelResponse::factory();
        }

        if (!($result instanceof ApiModelResponse)) {
            throw new ApiModelException('Api model method must return ApiModelResponse objects only');
        }

        return $result;
    }

    // TODO
    protected function get_named_method_args($class, $method, array $runtime_arguments = NULL)
    {
        $reflector  = new ReflectionMethod($class, $method);
        $parameters = $reflector->getParameters();

        $args = [];

        foreach ($parameters as $parameter) {
            $position = $parameter->getPosition();

            if (!$runtime_arguments || !isset($runtime_arguments[$position])) {
                continue;
            }

            $name        = $parameter->getName();
            $args[$name] = $runtime_arguments[$position];
        }

        return $args;
    }
}

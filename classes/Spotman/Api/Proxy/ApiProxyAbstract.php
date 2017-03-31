<?php
namespace Spotman\Api\Proxy;

use ReflectionMethod;
use Spotman\Api\ApiModelException;
use Spotman\Api\ApiModelInterface;
use Spotman\Api\ApiModelResponse;
use Spotman\Api\ApiProxyException;
use Spotman\Api\ApiProxyInterface;

abstract class ApiProxyAbstract implements ApiProxyInterface
{
    /**
     * @var \Spotman\Api\ApiModelInterface
     */
    protected $model;

    /**
     * ApiProxyAbstract constructor.
     *
     * @param \Spotman\Api\ApiModelInterface $model
     */
    public function __construct(ApiModelInterface $model)
    {
        $this->model = $model;
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
        $model = $this->model;

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
            throw new ApiModelException('Api model method may return objects which are instances of :interface only', [
                ':interface' => ApiModelResponse::class,
            ]);
        }

        return $result;
    }

    // TODO
    protected function get_named_method_args($class, $method, array $runtime_arguments = null)
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

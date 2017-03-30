<?php
namespace Spotman\Api;

class ApiModelFactory
{
    /**
     * @param string $name
     *
     * @return \Spotman\Api\ApiModelInterface
     * @throws \Spotman\Api\ApiModelException
     */
    public function create($name)
    {
        if (!$name) {
            throw new ApiModelException('Model name required');
        }

        // TODO namespace based factory + move all api models into namespaces
        $className = 'API_Model_'.$name;

        if (!class_exists($className)) {
            throw new ApiModelException('Can not find model class :class', [':class' => $className]);
        }

        /** @var \Spotman\Api\ApiModelInterface $model */
        $model = new $className;

        if (!($model instanceof ApiModelInterface)) {
            throw new ApiModelException('The class :class must be the instance of :interface', [
                ':class'        => get_class($model),
                ':interface'    =>  ApiModelInterface::class,
            ]);
        }

        return $model;
    }
}

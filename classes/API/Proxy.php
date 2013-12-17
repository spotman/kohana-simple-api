<?php defined('SYSPATH') OR die('No direct script access.');

abstract class API_Proxy {

    const INTERNAL = 1;
    const EXTERNAL = 2;

    protected static $_type_to_class_name = array(
        self::INTERNAL  =>  'Internal',
        self::EXTERNAL  =>  'External',
    );

    protected static $_allowed_model_result_types = array('null', 'boolean', 'string', 'integer', 'double');

    protected $_model;

    /**
     * @param int $type
     * @return static
     */
    public static function by_type($type)
    {
        $name = static::$_type_to_class_name[$type];
        return static::factory($name);
    }

    /**
     * @param $name
     * @return static
     */
    public static function factory($name)
    {
        $class_name = 'API_Proxy_'.$name;
        return new $class_name;
    }

    /**
     * @param API_Model|null $model
     * @return $this|API_Model
     */
    public function model(API_Model $model = NULL)
    {
        if ( $model === NULL )
            return $this->_model;

        $this->_model = $model;
        return $this;
    }

    final public function __call($method, $arguments)
    {
        return $this->call($method, $arguments);
    }

    abstract protected function call($method, array $arguments);

    protected function model_call($method, array $arguments)
    {
        $model = $this->model();

        if ( ! is_callable(array($model, $method)) )
            throw new API_Proxy_Exception('Unknown method :method in proxy object :class',
                array(':method' => $method, ':class' => get_class($model)));

        // TODO deal with missed/unordered arguments

        $result = call_user_func_array(array($model, $method), $arguments);

        return $this->convert_result($result);
    }

    protected function convert_result($model_call_result)
    {
        if ( is_object($model_call_result) )
        {
            return $this->convert_result_object($model_call_result);
        }
        else if ( is_array($model_call_result) )
        {
            return $this->convert_result_traversable($model_call_result);
        }
        else
        {
            return $this->convert_result_simple($model_call_result);
        }
    }

    protected function convert_result_object($object)
    {
        if ( $object instanceof API_Model_Result )
        {
            return $object->get_api_result_data();
        }
        else if ( $object instanceof Traversable )
        {
            return $this->convert_result_traversable($object);
        }
        else
            throw new API_Model_Exception(
                'Api model method may return objects implementing Traversable or API_Model_Result only'
            );
    }

    protected function convert_result_traversable($traversable)
    {
        $data = array();

        foreach ( $traversable as $key => $value )
        {
            $data[$key] = $this->convert_result($value);
        }

        return $data;
    }

    protected function convert_result_simple($data)
    {
        $type = gettype($data);

        if ( ! in_array(strtolower($type), static::$_allowed_model_result_types) )
            throw new API_Model_Exception('API model must not return values of type :type', array(':type' => $type));

        return $data;
    }

    // TODO
    protected function get_named_method_args($class, $method, array $runtime_arguments = array())
    {
        $reflector = new ReflectionMethod($class, $method);
        $parameters = $reflector->getParameters();

        $args = array();

        foreach ( $parameters as $parameter )
        {
            $position = $parameter->getPosition();

            if ( ! isset( $runtime_arguments[ $position ] ) )
                continue;

            $name = $parameter->getName();
            $args[ $name ] = $runtime_arguments[ $position ];
        }

        return $args;
    }

}

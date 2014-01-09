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
        $data = $this->call($method, $arguments);

        return API_Response::factory()->from_array($data);
    }

    /**
     * @param $method
     * @param array $arguments
     * @return array
     */
    abstract protected function call($method, array $arguments);

    protected function model_call($method, array $arguments)
    {
        $model = $this->model();

        if ( ! is_callable(array($model, $method)) )
            throw new API_Proxy_Exception('Unknown method :method in proxy object :class',
                array(':method' => $method, ':class' => get_class($model)));

        // TODO deal with missed/unordered arguments

        /** @var API_Response $result */
        $result = call_user_func_array(array($model, $method), $arguments);

        // For model methods without response
        if ( $result === NULL )
        {
            $result = API::response();
        }
//        else if ( ! ($result instanceof API_Response) )
//        {
//            $result = API::response()->set_data($result);
//        }

        if ( ! ($result instanceof API_Response) )
            throw new API_Model_Exception(
                'Api model method must return API_Response objects only'
            );

        return $result->as_array();
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

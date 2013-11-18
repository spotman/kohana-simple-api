<?php defined('SYSPATH') OR die('No direct script access.');

abstract class API_Proxy {

    const INTERNAL = 1;
    const EXTERNAL = 2;

    protected static $_type_to_class_name = array(
        self::INTERNAL  =>  'Internal',
        self::EXTERNAL  =>  'External',
    );

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

    public function model($model = NULL)
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

    abstract protected function call($method, $arguments);

    protected function model_call($method, $arguments)
    {
        $object = $this->model();

        if ( ! method_exists($object, $method) )
            throw new API_Proxy_Exception('Unknown method :method in proxy object :class',
                array(':method' => $method, ':class' => get_class($object)));

        $result = call_user_func_array(array($object, $method), $arguments);

        API_Model::check_result_type($result);

        return $result;
    }

}


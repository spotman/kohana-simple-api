<?php defined('SYSPATH') OR die('No direct script access.');


abstract class Core_API_Transport {

    const JSON_RPC = 1;

    protected static $_type_to_class_name = array(
        self::JSON_RPC  =>  'JsonRPC',
    );

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
        $class_name = 'API_Transport_'.$name;
        return new $class_name;
    }

    /**
     * @param $resource
     * @param $method
     * @param $arguments
     * @return mixed
     */
    abstract public function remote_procedure_call($resource, $method, $arguments);

}

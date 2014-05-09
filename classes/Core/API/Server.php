<?php defined('SYSPATH') OR die('No direct script access.');


abstract class Core_API_Server extends API_Types {

    /**
     * @param int $type
     * @return static
     * @throws API_Exception
     */
    public static function by_type($type)
    {
        $name = static::type_to_name($type);
        return static::factory($name);
    }

    /**
     * @param $name
     * @return static
     * @throws API_Exception
     */
    public static function factory($name)
    {
        $class_name = 'API_Server_'.$name;

        if ( ! class_exists($class_name) )
            throw new API_Exception('Can not find API server for :name', array(':name' => $name));

        return new $class_name;
    }

    /**
     * Process API request and push data to $response
     *
     * @param Request $request
     * @param Response $response
     */
    abstract public function process(Request $request, Response $response);

}

<?php defined('SYSPATH') OR die('No direct script access.');


abstract class API_Model {

    protected static $_allowed_result_types = array('null', 'boolean', 'string', 'integer', 'double');

    /**
     * @param null $name
     * @return static
     */
    public static function factory($name = NULL)
    {
        if ( ! $name )
            return new static;

        $class_name = __CLASS__.'_'.$name;
        return new $class_name;
    }

    public static function check_result_type($data)
    {
        if ( is_object($data) )
        {
            static::check_result_object($data);
        }
        else
        {
            static::check_result_item_type($data);
        }
    }

    protected static function check_result_object($object)
    {
//        if ( $object instanceof Iterator )
//        {
//            // Check the first element (allow nested iterators)
//            $item = $data->current();
//            static::check_result_type($item);
//        }
//        else
        throw new API_Model_Exception('Api model method cannot return objects of class :class',
            array(':class' => get_class($object)));
    }

    protected static function check_result_item_type($data)
    {
        $type = gettype($data);

        if ( ! in_array(strtolower($type), static::$_allowed_result_types) )
            throw new API_Model_Exception('API model can not return values of type :type', array(':type' => $type));
    }
}

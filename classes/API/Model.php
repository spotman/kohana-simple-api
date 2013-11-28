<?php defined('SYSPATH') OR die('No direct script access.');


abstract class API_Model {

    /**
     * @var string Model name
     */
    protected $_name;

    protected static $_allowed_result_types = array('null', 'boolean', 'string', 'integer', 'double');

    /**
     * @param null $name
     * @return static
     */
    public static function factory($name = NULL)
    {
        // TODO Why this?
        if ( ! $name )
            return new static;

        $class_name = __CLASS__.'_'.$name;

        /** @var API_Model $object */
        $object = new $class_name;

        return $object->name($name);
    }

    /**
     * @param string|null $value
     * @return $this|string
     */
    public function name($value = NULL)
    {
        if ( $value === NULL )
            return $this->_name;

        $this->_name = $value;
        return $this;
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

    // TODO
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

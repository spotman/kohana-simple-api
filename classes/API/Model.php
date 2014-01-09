<?php defined('SYSPATH') OR die('No direct script access.');


abstract class API_Model {

    /**
     * @var string Model name
     */
    protected $_name;

    /**
     * @param string|null $name
     * @return static
     * @throws API_Model_Exception
     */
    public static function factory($name)
    {
        if ( $name )
        {
            $class_name = __CLASS__.'_'.$name;

            /** @var API_Model $object */
            $object = new $class_name;
        }
        // Allow concrete initialization via Api_Model_User::factory()
        else
        {
            /** @var API_Model $object */
            $object = new static;
        }

        if ( ! ($object instanceof API_Model) )
            throw new API_Model_Exception('The class :class must be the instance of API_Model',
                array(':class' => get_class($object))
            );

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

    /**
     * @param $data
     * @return API_Response
     */
    protected function response($data)
    {
        return API::response()->set_data($data);
    }

}

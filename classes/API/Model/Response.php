<?php defined('SYSPATH') OR die('No direct script access.');

class API_Model_Result {

    protected $_data;

    /**
     * Timestamp of the last changes
     * @var
     */
    protected $_last_modified;

    /**
     * @var int|null Count of items in original result (when custom filtering like "offset" and "skip" was used)
     */
    protected $_total = NULL;

    public static function factory($data)
    {
        return new static($data);
    }

    public function __construct($_data)
    {
        $this->_data = $_data;
    }

//
//    /**
//     * @param mixed $data
//     */
//    public function set_data($data)
//    {
//        // TODO
//        $this->_data = $data;
//    }

    /**
     * @return mixed
     */
    public function get_data()
    {
        return $this->_data;
    }

    /**
     * @param mixed $last_modified
     */
    public function set_last_modified($last_modified)
    {
        $this->_last_modified = $last_modified;
    }

    /**
     * @return mixed
     */
    public function get_last_modified()
    {
        return $this->_last_modified;
    }

    public static function from_array(array $input)
    {
        // TODO
    }

    public function as_array()
    {
        // TODO
        return array(
            'data'  =>  $this->get_data(),
            'last_modified'  =>  $this->get_last_modified(),
        );
    }

}
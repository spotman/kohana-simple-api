<?php defined('SYSPATH') OR die('No direct script access.');

class API_Response {

    protected $_data;

    /**
     * @var DateTime Timestamp of the last changes
     */
    protected $_last_modified;

    /**
     * @var int|null Count of items in original result (when custom filtering like "offset" and "skip" was used)
     */
    protected $_total = NULL;

    protected static $_allowed_result_types = array('null', 'boolean', 'string', 'integer', 'double');

    public static function factory()
    {
        return new static;
    }

    /**
     * @param $data
     * @return self
     */
    public function set_data($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function get_data()
    {
        return $this->_data;
    }

    /**
     * @param DateTime $last_modified
     * @return $this
     */
    public function set_last_modified(DateTime $last_modified)
    {
        $this->_last_modified = $last_modified;
        return $this;
    }

    /**
     * @return NULL|DateTime
     */
    public function get_last_modified()
    {
        return $this->_last_modified ?: new DateTime;
    }

    public function from_array(array $input)
    {
        $data = isset($input['data'])
            ? $input['data']
            : NULL;

        $last_modified_timestamp = isset($input['last_modified'])
            ? $input['last_modified']
            : NULL;

//        if ( ! $data )
//            throw new API_Response_Exception('Data is missing');

        if ( ! $last_modified_timestamp )
            throw new API_Response_Exception('Last modified time is missing');

        $last_modified_object = (new DateTime())->setTimestamp($last_modified_timestamp);

        return $this
            ->set_data($data)
            ->set_last_modified($last_modified_object);
    }

    public function as_array()
    {
        $data = $this->convert_result($this->get_data());

        $last_modified = $this->get_last_modified() ?: (new DateTime);

        return array(
            'data'  =>  $data,
            'last_modified'  => $last_modified->getTimestamp(),
        );
    }

    protected function process_last_modified(DateTime $new_last_modified)
    {
        $current_timestamp = $this->_last_modified ? $this->_last_modified->getTimestamp() : NULL;

        if ( $new_last_modified->getTimestamp() > $current_timestamp )
        {
            $this->_last_modified = $new_last_modified;
        }
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

    /**
     * @param $object
     * @returns int|string|array
     * @throws API_Model_Exception
     */
    protected function convert_result_object($object)
    {
        if ( $object instanceof API_Response_Item )
        {
            // Get item`s last modified time for setting it in current response
            $last_modified = $object->get_last_modified();

            if ( $last_modified )
            {
                $this->process_last_modified($last_modified);
            }

            return $object->get_api_response_data();
        }
        else if ( $object instanceof Traversable )
        {
            return $this->convert_result_traversable($object);
        }
        else
            throw new API_Model_Exception(
                'API model method may return objects implementing Traversable or API_Model_Response_Item only'
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

        if ( ! in_array(strtolower($type), static::$_allowed_result_types) )
            throw new API_Model_Exception('API model must not return values of type :type', array(':type' => $type));

        return $data;
    }

}

<?php defined('SYSPATH') OR die('No direct script access.');


abstract class Core_API_Model {

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
        if ( ! $name )
            throw new API_Model_Exception('Model name required');

        $class_name = 'API_Model_'.$name;

        if ( ! class_exists($class_name) )
            throw new API_Model_Exception('Can not find model class :class_name', array(':class_name' => $class_name));

        /** @var API_Model $object */
        $object = new $class_name;

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

    public function one($id)
    {
        return $this->response( $this->_one( (int) $id ) );
    }

    /**
     * Override this if needed
     *
     * @param $id
     * @return Model
     */
    protected function _one($id)
    {
        return $this->model($id);
    }

    public function save($data)
    {
        $id = ( isset($data->id) AND (int) $data->id )
            ? (int) $data->id
            : NULL;

        $model = $this->model($id);

        $response_data = $this->_save($model, $data);

        return $this->response($response_data);
    }

    /**
     * Override this method
     *
     * @param Model $model
     * @param $data
     * @throws HTTP_Exception_501
     * @return mixed|NULL
     */
    protected function _save($model, $data)
    {
        throw new HTTP_Exception_501;
    }

    public function delete($id)
    {
        $model = $this->model( (int) $id );

        return $this->response( $this->_delete($model) );
    }

    /**
     * Override this if needed
     *
     * @param Model $model
     * @throws HTTP_Exception_501
     * @return bool
     */
    protected function _delete($model)
    {
        throw new HTTP_Exception_501;
    }

    /**
     * Returns new model or performs search by id
     *
     * @param null $id
     * @return Model
     */
    abstract protected function model($id = NULL);

    /**
     * Creates API response from raw data (or without it)
     *
     * @param mixed|NULL $data
     * @return API_Response
     */
    protected function response($data = NULL)
    {
        return API::response()->set_data($data);
    }

}

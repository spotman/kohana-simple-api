<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Core_API_Client extends API_Types {

    /**
     * @var string Hostname of the requesting API
     */
    protected $host;

    /**
     * @var int Version of the requesting API
     */
    protected $version;

    /**
     * @param int $type
     * @param string $host
     * @param int $version
     * @return API_Client
     * @throws API_Exception
     */
    public static function by_type($type, $host, $version)
    {
        $name = static::type_to_name($type);
        $class_name = 'API_Client_'.$name;

        if ( ! class_exists($class_name) )
            throw new API_Exception('Can not find API client for :name', array(':name' => $name));

        return new $class_name($type, $host, $version);
    }

    public function __construct($type, $host, $version)
    {
        $this->type    = $type;
        $this->host    = $host;
        $this->version = $version;
    }

    protected function get_url()
    {
        $relative_url =  Route::url('api', array(
            'version'   =>  $this->version,
            'type'      =>  static::type_to_url_key($this->type),
        ));

        return $this->host.$relative_url;
    }

    /**
     * @param $resource
     * @param $method
     * @param array $arguments
     * @return API_Response
     */
    abstract public function remote_procedure_call($resource, $method, array $arguments);

}
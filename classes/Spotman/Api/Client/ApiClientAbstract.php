<?php
namespace Spotman\Api\Client;

use Route;
use Spotman\Api\ApiTypesHelper;
use Spotman\Api\ApiClientInterface;

abstract class ApiClientAbstract implements ApiClientInterface
{
    /**
     * @var int
     */
    protected $type;

    /**
     * @var string Hostname of the requesting API
     */
    protected $host;

    /**
     * @var int Version of the requesting API
     */
    protected $version;

    public function __construct($type, $host, $version)
    {
        $this->type    = $type;
        $this->host    = $host;
        $this->version = $version;
    }

    /**
     * @deprecated Move to ApiHelper and exclude url creating logic to controller
     * @return string
     */
    public function get_url()
    {
        $relative_url = Route::url('api', [
            'version' => $this->version,
            'type'    => ApiTypesHelper::typeToUrlKey($this->type),
        ]);

        return $this->host . $relative_url;
    }
}

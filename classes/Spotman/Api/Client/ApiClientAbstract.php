<?php
namespace Spotman\Api\Client;

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

    public function __construct(int $type, string $host, int $version)
    {
        $this->type    = $type;
        $this->host    = $host;
        $this->version = $version;
    }
}

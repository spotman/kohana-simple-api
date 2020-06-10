<?php
namespace Spotman\Api;

use Psr\Http\Server\RequestHandlerInterface;

interface ApiServerInterface extends RequestHandlerInterface
{
    public const API_VERSION_REQUEST_ATTR = '__API_VERSION__';
}

<?php

use Spotman\Api\ApiFacade;
use Spotman\Api\ApiTypesHelper;

class Controller_API extends Controller
{
    /**
     * @Inject
     * @var ApiFacade
     */
    private $api;

    public function action_process(): void
    {
        $serverKey     = $this->request->param('type');
        $serverVersion = (int)$this->request->param('version');
        $serverType    = ApiTypesHelper::urlKeyToType($serverKey);

        $server = $this->api->createServer($serverType, $serverVersion);

        $server->process($this->api, $this->request, $this->response);
    }
}

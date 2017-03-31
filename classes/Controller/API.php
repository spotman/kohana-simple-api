<?php

use Spotman\Api\API;
use Spotman\Api\ApiServerFactory;
use Spotman\Api\ApiTypesHelper;

class Controller_API extends Controller
{
    public function action_process()
    {
        $serverKey = $this->request->param('type');
        $serverType = ApiTypesHelper::urlKeyToType($serverKey);

        $serverVersion = (int) $this->request->param('version');

        $api = new API;
        $server = $api->serverFactory($serverType, $serverVersion);

        $server->process($this->request, $this->response);
    }
}

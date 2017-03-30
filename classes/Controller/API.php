<?php

use Spotman\Api\API;
use Spotman\Api\ApiServerFactory;
use Spotman\Api\ApiTypesHelper;

class Controller_API extends Controller
{
    public function action_process()
    {
        $this->getServer()->process(, $this->request, $this->response);
    }

    protected function getServer()
    {
        if (!API::isServerEnabled()) {
            throw new HTTP_Exception_501('API is not implemented');
        }

        $serverKey = $this->request->param('type');
        $serverType = ApiTypesHelper::urlKeyToType($serverKey);

        $serverVersion = (int) $this->request->param('version');

        $factory = new ApiServerFactory;

        return $factory->createApiServerByType($serverType, $serverVersion);
    }
}

<?php

use Spotman\Api\API;
use Spotman\Api\ApiTypesHelper;

class Controller_API extends Controller
{
    public function action_process()
    {
        $serverKey     = $this->request->param('type');
        $serverVersion = (int)$this->request->param('version');
        $serverType    = ApiTypesHelper::urlKeyToType($serverKey);

        /** @var API $api */
        $api    = $this->getContainer()->get(API::class);
        $server = $api->createServer($serverType, $serverVersion);

        $server->process($api, $this->request, $this->response);
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    protected function getContainer()
    {
        return \BetaKiller\DI\Container::getInstance();
    }
}

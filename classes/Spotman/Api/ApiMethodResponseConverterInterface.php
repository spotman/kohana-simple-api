<?php
declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Model\UserInterface;

interface ApiMethodResponseConverterInterface
{
    /**
     * @param \Spotman\Api\ApiMethodResponse  $response
     * @param \BetaKiller\Model\UserInterface $user
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    public function convert(
        ApiMethodResponse $response,
        UserInterface $user
    ): ApiMethodResponse;
}

<?php
declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Model\LanguageInterface;
use BetaKiller\Model\UserInterface;

interface ApiMethodResponseConverterInterface
{
    /**
     * @param \Spotman\Api\ApiMethodResponse      $response
     * @param \BetaKiller\Model\UserInterface     $user
     * @param \BetaKiller\Model\LanguageInterface $lang
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    public function convert(
        ApiMethodResponse $response,
        UserInterface $user,
        LanguageInterface $lang
    ): ApiMethodResponse;
}

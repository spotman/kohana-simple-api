<?php
declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Model\AbstractEntityInterface;
use BetaKiller\Model\LanguageInterface;
use BetaKiller\Model\UserInterface;

interface ApiMethodResponseConverterInterface
{
    /**
     * @param \Spotman\Api\ApiMethodInterface                $method
     *
     * @param \Spotman\Api\ApiMethodResponse                 $response
     * @param \BetaKiller\Model\UserInterface                $user
     * @param \BetaKiller\Model\LanguageInterface            $lang
     * @param \BetaKiller\Model\AbstractEntityInterface|null $entity
     *
     * @return \Spotman\Api\ApiMethodResponse
     */
    public function convert(
        ApiMethodInterface $method,
        ApiMethodResponse $response,
        UserInterface $user,
        LanguageInterface $lang,
        ?AbstractEntityInterface $entity
    ): ApiMethodResponse;
}

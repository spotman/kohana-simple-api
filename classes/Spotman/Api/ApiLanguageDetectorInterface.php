<?php
declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Model\LanguageInterface;
use BetaKiller\Model\UserInterface;
use Spotman\Defence\ArgumentsInterface;

interface ApiLanguageDetectorInterface
{
    /**
     * @param \Spotman\Api\ApiMethodInterface     $instance
     * @param \Spotman\Defence\ArgumentsInterface $arguments
     * @param \BetaKiller\Model\UserInterface     $user
     *
     * @return \BetaKiller\Model\LanguageInterface
     */
    public function detect(
        ApiMethodInterface $instance,
        ArgumentsInterface $arguments,
        UserInterface $user
    ): LanguageInterface;
}

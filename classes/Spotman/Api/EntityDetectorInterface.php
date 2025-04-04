<?php

declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Api\Method\EntityBasedApiMethodInterface;
use BetaKiller\Model\AbstractEntityInterface;
use Spotman\Defence\ArgumentsInterface;

interface EntityDetectorInterface
{
    public function getEntity(EntityBasedApiMethodInterface $method, ArgumentsInterface $arguments): AbstractEntityInterface;
}

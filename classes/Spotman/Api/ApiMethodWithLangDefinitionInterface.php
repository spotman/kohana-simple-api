<?php
declare(strict_types=1);

namespace Spotman\Api;

use BetaKiller\Model\LanguageInterface;
use Spotman\Defence\ArgumentsInterface;

interface ApiMethodWithLangDefinitionInterface
{
    public function detectLanguage(ArgumentsInterface $arguments): ?LanguageInterface;
}

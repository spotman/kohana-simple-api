<?php
declare(strict_types=1);

namespace Spotman\Defence;

class ArgumentRule implements ArgumentRuleInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $isOptional;

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * ArgumentRule constructor.
     *
     * @param string $name
     * @param string $type
     * @param bool   $isOptional
     * @param mixed  $defaultValue
     */
    public function __construct(string $name, string $type, bool $isOptional = null, $defaultValue = null)
    {
        $this->name         = $name;
        $this->type         = $type;
        $this->isOptional   = $isOptional ?? false;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Returns true if rule defines identity argument
     *
     * @return bool
     */
    public function isIdentity(): bool
    {
        return $this->getType() === self::TYPE_IDENTITY;
    }

    /**
     * Returns true if rule defines boolean argument
     *
     * @return bool
     */
    public function isBool(): bool
    {
        return $this->getType() === self::TYPE_BOOLEAN;
    }

    /**
     * Returns true if rule defines integer argument
     *
     * @return bool
     */
    public function isInt(): bool
    {
        return $this->getType() === self::TYPE_INTEGER;
    }

    /**
     * Returns true if rule defines string argument
     *
     * @return bool
     */
    public function isString(): bool
    {
        return $this->getType() === self::TYPE_STRING;
    }

    /**
     * Returns true if rule defines a string containing email
     *
     * @return bool
     */
    public function isEmail(): bool
    {
        return $this->getType() === self::TYPE_EMAIL;
    }

    /**
     * Returns true if rule defines string containing html
     *
     * @return bool
     */
    public function isHtml(): bool
    {
        return $this->getType() === self::TYPE_HTML;
    }

    /**
     * Returns true if rule defines array argument
     *
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->getType() === self::TYPE_ARRAY;
    }
}

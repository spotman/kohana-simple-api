<?php
declare(strict_types=1);

namespace Spotman\Api;

interface ArgumentRuleInterface
{
    public const TYPE_IDENTITY = 'id';
    public const TYPE_BOOLEAN  = 'bool';
    public const TYPE_INTEGER  = 'int';
    public const TYPE_STRING   = 'string';
    public const TYPE_EMAIL    = 'email';
    public const TYPE_HTML     = 'html';
    public const TYPE_ARRAY    = 'array';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return bool
     */
    public function isOptional(): bool;

    /**
     * @return mixed|null
     */
    public function getDefaultValue();

    /**
     * Returns true if rule defines identity argument
     *
     * @return bool
     */
    public function isIdentity(): bool;

    /**
     * Returns true if rule defines a boolean argument
     *
     * @return bool
     */
    public function isBool(): bool;

    /**
     * Returns true if rule defines an integer argument
     *
     * @return bool
     */
    public function isInt(): bool;

    /**
     * Returns true if rule defines a string argument
     *
     * @return bool
     */
    public function isString(): bool;

    /**
     * Returns true if rule defines a string containing html
     *
     * @return bool
     */
    public function isHtml(): bool;

    /**
     * Returns true if rule defines a string containing email
     *
     * @return bool
     */
    public function isEmail(): bool;

    /**
     * Returns true if rule defines an array argument
     *
     * @return bool
     */
    public function isArray(): bool;
}

<?php
declare(strict_types=1);

namespace Spotman\Defence;

interface ArgumentsDefinitionInterface
{
    /**
     * Define ID argument
     *
     * @param string|null $name
     * @param bool|null   $optional
     *
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function identity(string $name = null, bool $optional = null): ArgumentsDefinitionInterface;

    /**
     * Define int argument
     *
     * @param string    $name
     * @param bool|null $optional
     * @param null      $default
     *
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function int(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface;

    /**
     * Define string argument
     *
     * @param string    $name
     * @param bool|null $optional
     * @param null      $default
     *
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function string(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface;

    /**
     * Define string argument containing email
     *
     * @param string    $name
     * @param bool|null $optional
     * @param null      $default
     *
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function email(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface;

    /**
     * Define string argument containing HTML code
     *
     * @param string    $name
     * @param bool|null $optional
     * @param null      $default
     *
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function html(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface;

    /**
     * Define bool argument
     *
     * @param string    $name
     * @param bool|null $optional
     * @param null      $default
     *
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function bool(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface;

    /**
     * Define array argument
     *
     * @param string    $name
     * @param bool|null $optional
     * @param null      $default
     *
     * @return \Spotman\Defence\ArgumentsDefinitionInterface
     */
    public function array(string $name, bool $optional = null, $default = null): ArgumentsDefinitionInterface;

    /**
     * @return ArgumentRuleInterface[]
     */
    public function getRules(): array;
}

<?php
declare(strict_types=1);

namespace Spotman\Api;

interface ArgumentsInterface
{
    public const IDENTITY_KEY = 'id';

    /**
     * @return null|string
     */
    public function getID(): ?string;

    /**
     * Returns true if current arguments set contains identity value
     *
     * @return bool
     */
    public function hasID(): bool;

    /**
     * Returns true if current arguments set contains value for provided key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     *
     * @return int
     */
    public function getInt(string $key): int;

    /**
     * @param string $key
     *
     * @return string
     */
    public function getString(string $key): string;

    /**
     * @param string $key
     *
     * @return string
     */
    public function getLowercase(string $key): string;

    /**
     * @param string $key
     *
     * @return string
     */
    public function getUppercase(string $key): string;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function getBool(string $key): bool;

    /**
     * @param string $key
     *
     * @return array
     */
    public function getArray(string $key): array;
}

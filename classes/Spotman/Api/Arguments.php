<?php
declare(strict_types=1);

namespace Spotman\Api;

class Arguments implements ArgumentsInterface
{
    /**
     * @var array
     */
    private $args;

    public function __construct(array $array)
    {
        $this->args = $array;
    }

    public function getID(): ?string
    {
        $id = $this->detectID();

        if (!$id) {
            throw new \InvalidArgumentException('Missing identity value');
        }

        return $id;
    }

    /**
     * Returns true if current arguments set contains identity value
     *
     * @return bool
     */
    public function hasID(): bool
    {
        return (bool)$this->detectID();
    }

    /**
     * Returns true if current arguments set contains value for provided key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->args[$key]);
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function getInt(string $key): int
    {
        return (int)$this->args[$key];
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getString(string $key): string
    {
        return trim((string)$this->args[$key]);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getLowercase(string $key): string
    {
        return \mb_strtolower($this->getString($key));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getUppercase(string $key): string
    {
        return \mb_strtoupper($this->getString($key));
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function getBool(string $key): bool
    {
        return (bool)$this->args[$key];
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getArray(string $key): array
    {
        return (array)$this->args[$key];
    }

    private function detectID(): ?string
    {
        if (!empty($this->args[self::IDENTITY_KEY])) {
            return (string)$this->args[self::IDENTITY_KEY];
        }

        $first = \reset($this->args);

        if (\is_array($first) && !empty($first[self::IDENTITY_KEY])) {
            return (string)$first[self::IDENTITY_KEY];
        }

        if (\is_object($first) && !empty($first->{self::IDENTITY_KEY})) {
            return (string)$first->{self::IDENTITY_KEY};
        }

        return null;
    }
}

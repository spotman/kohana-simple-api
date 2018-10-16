<?php
declare(strict_types=1);

namespace Spotman\Api;

use Psr\Http\Message\ServerRequestInterface;

final class Route
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var int
     */
    private $type;

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $version = (int)$request->getAttribute('version');
        $typeKey = (string)$request->getAttribute('type');

        $type = ApiTypesHelper::urlKeyToType($typeKey);

        return new self($version, $type);
    }

    /**
     * Route constructor.
     *
     * @param int    $version
     * @param int $type
     */
    public function __construct(int $version, int $type)
    {
        $this->version = $version;
        $this->type    = $type;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    public function asUrlPath(): string
    {
        return '/api/v'.$this->version.'/'.ApiTypesHelper::typeToUrlKey($this->type);
    }

    public function __toString(): string
    {
        return $this->asUrlPath();
    }
}

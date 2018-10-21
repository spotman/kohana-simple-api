<?php
namespace Spotman\Api;

final class ApiTypesHelper
{
    public const JSON_RPC = 1;

    /**
     * @var string[]
     */
    private static $typeToName = [
        self::JSON_RPC => 'JsonRpc',
    ];

    /**
     * @var string[]
     */
    private static $typeToUrlKey = [
        self::JSON_RPC => 'json-rpc',
    ];

    /**
     * @param int $itemType
     *
     * @return string
     * @throws \Spotman\Api\ApiException
     */
    public static function typeToName(int $itemType): string
    {
        if (!isset(static::$typeToName[$itemType])) {
            throw new ApiException('Undefined type ":type"', [':type' => $itemType]);
        }

        return static::$typeToName[$itemType];
    }

    /**
     * @param int $itemType
     *
     * @return string
     * @throws \Spotman\Api\ApiException
     */
    public static function typeToUrlKey(int $itemType): string
    {
        if (!isset(static::$typeToUrlKey[$itemType])) {
            throw new ApiException('Undefined type: ":type"', [':type' => $itemType]);
        }

        return static::$typeToUrlKey[$itemType];
    }

    /**
     * @param $itemUrlKey
     *
     * @return int
     * @throws \Spotman\Api\ApiException
     */
    public static function urlKeyToType(string $itemUrlKey): int
    {
        foreach (static::$typeToUrlKey as $type => $key) {
            if ($itemUrlKey === $key) {
                return $type;
            }
        }

        throw new ApiException('Unknown url key ":key"', [':key' => $itemUrlKey]);
    }

    public static function nameToType(string $itemName): int
    {
        foreach (static::$typeToName as $type => $key) {
            if ($itemName === $key) {
                return $type;
            }
        }

        throw new ApiException('Unknown name ":name"', [':name' => $itemName]);
    }
}

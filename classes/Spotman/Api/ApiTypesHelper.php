<?php

namespace Spotman\Api;

final class ApiTypesHelper
{
    public const JSON_RPC   = 1;
    public const JSON_PLAIN = 2;

    /**
     * @var string[]
     */
    private static array $typeToName = [
        self::JSON_RPC   => 'JsonRpc',
        self::JSON_PLAIN => 'JsonPlain',
    ];

    /**
     * @var string[]
     */
    private static array $typeToUrlKey = [
        self::JSON_RPC   => 'json-rpc',
        self::JSON_PLAIN => 'json',
    ];

    /**
     * @param int $itemType
     *
     * @return string
     * @throws \Spotman\Api\ApiException
     */
    public static function typeToName(int $itemType): string
    {
        if (!isset(self::$typeToName[$itemType])) {
            throw new ApiException('Undefined type ":type"', [':type' => $itemType]);
        }

        return self::$typeToName[$itemType];
    }

    /**
     * @param int $itemType
     *
     * @return string
     * @throws \Spotman\Api\ApiException
     */
    public static function typeToUrlKey(int $itemType): string
    {
        if (!isset(self::$typeToUrlKey[$itemType])) {
            throw new ApiException('Undefined type: ":type"', [':type' => $itemType]);
        }

        return self::$typeToUrlKey[$itemType];
    }

    /**
     * @param string $itemUrlKey
     *
     * @return int
     * @throws \Spotman\Api\ApiException
     */
    public static function urlKeyToType(string $itemUrlKey): int
    {
        foreach (self::$typeToUrlKey as $type => $key) {
            if ($itemUrlKey === $key) {
                return $type;
            }
        }

        throw new ApiException('Unknown url key ":key"', [':key' => $itemUrlKey]);
    }

    /**
     * @param string $itemName
     *
     * @return int
     * @throws \Spotman\Api\ApiException
     */
    public static function nameToType(string $itemName): int
    {
        foreach (self::$typeToName as $type => $key) {
            if ($itemName === $key) {
                return $type;
            }
        }

        throw new ApiException('Unknown name ":name"', [':name' => $itemName]);
    }
}

<?php
namespace Spotman\Api;

class ApiTypesHelper
{
    const JSON_RPC = 1;

    protected static $typeToName = [
        self::JSON_RPC => 'JsonRpc',
    ];

    protected static $typeToUrlKey = [
        self::JSON_RPC => 'json-rpc',
    ];

    /**
     * @param int $itemType
     *
     * @return string
     * @throws ApiException
     */
    public static function typeToName($itemType)
    {
        if (!isset(static::$typeToName[$itemType])) {
            throw new ApiException('Undefined type :type', [':type' => $itemType]);
        }

        return static::$typeToName[$itemType];
    }

    public static function typeToUrlKey($itemType)
    {
        if (!isset(static::$typeToUrlKey[$itemType])) {
            throw new ApiException('Undefined type: :type', [':type' => $itemType]);
        }

        return static::$typeToUrlKey[$itemType];
    }

    public static function urlKeyToType($itemUrlKey)
    {
        foreach (static::$typeToUrlKey as $type => $key) {
            if ($itemUrlKey === $key) {
                return $type;
            }
        }

        throw new ApiException('Unknown url key: :key', [':key' => $itemUrlKey]);
    }

    public static function nameToType($itemName)
    {
        foreach (static::$typeToName as $type => $key) {
            if ($itemName === $key) {
                return $type;
            }
        }

        throw new ApiException('Unknown name: :name', [':name' => $itemName]);
    }
}

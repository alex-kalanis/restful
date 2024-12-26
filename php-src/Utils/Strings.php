<?php

namespace kalanis\Restful\Utils;


use Nette;


/**
 * Strings util class
 * @package kalanis\Restful\Utils
 */
class Strings extends Nette\Utils\Strings
{

    /**
     * Converts string to PascalCase
     * @param string $string
     * @return string
     */
    public static function toPascalCase(string $string): string
    {
        return self::firstUpper(self::toCamelCase($string));
    }

    /**
     * Converts string to camelCase
     * @param string $string
     * @return string
     */
    public static function toCamelCase(string $string): string
    {
        $func = fn($matches): string => self::upper($matches[2]);

        return self::firstLower(self::replace($string, '/(_| |-)([a-zA-Z])/', $func));
    }

    /**
     * Converts first letter to lower case
     * @param string $s
     * @return string
     */
    public static function firstLower(string $s): string
    {
        return self::lower(self::substring($s, 0, 1)) . self::substring($s, 1);
    }

    /**
     * Converts string to snake_case
     * @param string $string
     * @return string
     */
    public static function toSnakeCase(string $string): string
    {
        $replace = [' ', '-'];
        return self::trim(
            self::lower(
                str_replace($replace, '_', self::replace(ltrim($string, '!'), '/([^_]+[a-z -]{1})([A-Z])/U', '$1_$2'))
            )
        );
    }
}

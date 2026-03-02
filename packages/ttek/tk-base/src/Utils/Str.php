<?php

namespace Tk\Utils;

class Str
{
    /**
     * create a short has of a string
     * used to create unique HTML id attributes
     */
    public static function shortHash(string $str, $length = 8): string
    {
        // Create a raw binary sha256 hash and base64 encode it.
        $hashBase64 = base64_encode(hash('sha256', $str, true));
        // Replace non-urlsafe chars to make the string urlsafe.
        $hashUrlsafe = str_replace(['+', '/', '=', '-', '_'], [''], $hashBase64);
        // Trim base64 padding characters from the end.
        $hashUrlsafe = rtrim($hashUrlsafe, '=');
        // Shorten the string before returning.
        return substr($hashUrlsafe, 0, $length);
    }

    /**
     * Convert to CamelCase so "test_func_name" would convert to "testFuncName"
     * Adds a capital at the first char and ass a space before all other upper case chars
     */
    public static function toCamel(string $str): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $str))));
    }

    /**
     * Convert to snake Case so "testFuncName" would convert to "test_func_name"
     */
    public static function toSnake(string $str, string $ch = '_'): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]+|(?<!^|\d)[\d]+/', $ch.'$0', $str));
    }

    /**
     * Convert camel case to words "testFunc" => "Test Func"
     */
    public static function camel2words(string $str): string
    {
        return ucfirst(preg_replace('/[A-Z]/', ' $0', $str));
    }

    /**
     * Is the string an HTML/XML string
     *
     * Returns true if the supplied string has any HTML tags
     */
    public static function isHtml(string $str): bool
    {
        return (strlen($str) != strlen(strip_tags($str)));
    }

    /**
     * prepend each line with an index number
     */
    public static function lineNumbers(string $str): string
    {
        $lines = explode("\n", $str);
        foreach ($lines as $i => $line) {
            $lines[$i] = ($i+1) . '  ' . $line;
        }
        return implode("\n", $lines);
    }

}

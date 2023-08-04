<?php

namespace Itt\Logger;

use RuntimeException;

final class Utils
{
    const DEFAULT_JSON_FLAGS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;

    /**
     * @param object $object
     * @return string
     * @internal
     */
    public static function getClass($object): string
    {
        $class = get_class($object);

        return 'c' === $class[0] && 0 === strpos($class, "class@anonymous\0") ? get_parent_class($class) . '@anonymous' : $class;
    }

    public static function substr(string $string, int $start, ?int $length = null)
    {
        if (extension_loaded('mbstring')) {
            return mb_strcut($string, $start, $length);
        }

        return substr($string, $start, $length);
    }

    public static function jsonEncode($data, ?int $encodeFlags = null, bool $ignoreErrors = false): string
    {
        if (null === $encodeFlags) {
            $encodeFlags = self::DEFAULT_JSON_FLAGS;
        }

        if ($ignoreErrors) {
            $json = @json_encode($data, $encodeFlags);
            if (false === $json) {
                return 'null';
            }

            return $json;
        }

        $json = json_encode($data, $encodeFlags);
        if (false === $json) {
            $json = self::handleJsonError(json_last_error(), $data);
        }

        return $json;
    }

    public static function handleJsonError(int $code, $data, ?int $encodeFlags = null): string
    {
        if ($code !== JSON_ERROR_UTF8) {
            self::throwEncodeError($code, $data);
        }

        if (is_string($data)) {
            self::detectAndCleanUtf8($data);
        } elseif (is_array($data)) {
            array_walk_recursive($data, array('Monolog\Utils', 'detectAndCleanUtf8'));
        } else {
            self::throwEncodeError($code, $data);
        }

        if (null === $encodeFlags) {
            $encodeFlags = self::DEFAULT_JSON_FLAGS;
        }

        $json = json_encode($data, $encodeFlags);

        if ($json === false) {
            self::throwEncodeError(json_last_error(), $data);
        }

        return $json;
    }

    private static function throwEncodeError(int $code, $data)
    {
        switch ($code) {
            case JSON_ERROR_DEPTH:
                $msg = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $msg = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $msg = 'Unexpected control character found';
                break;
            case JSON_ERROR_UTF8:
                $msg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $msg = 'Unknown error';
        }

        throw new RuntimeException('JSON encoding failed: ' . $msg . '. Encoding: ' . var_export($data, true));
    }

    private static function detectAndCleanUtf8(&$data)
    {
        if (is_string($data) && !preg_match('//u', $data)) {
            $data = preg_replace_callback(
                '/[\x80-\xFF]+/',
                function ($m) {
                    return utf8_encode($m[0]);
                },
                $data
            );
            $data = str_replace(
                ['¤', '¦', '¨', '´', '¸', '¼', '½', '¾'],
                ['€', 'Š', 'š', 'Ž', 'ž', 'Œ', 'œ', 'Ÿ'],
                $data
            );
        }
    }
}

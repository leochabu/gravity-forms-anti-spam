<?php


class sanitizer
{
    /** Returns a sanitized string.
     * @param $string
     * @return string
     */
    public static function sanitizeString($string): string
    {
        return wp_kses(esc_html(esc_attr(strip_tags($string))), []);
    }

    public static function  encrypt_id($integer): string
    {
        $data = strval($integer);
        return base64_encode($data);
    }

    public static function decrypt_id($encoded): false|string
    {
        return base64_decode($encoded);
    }
}
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

    /**
     * Encrypts an integer using base64 encoding.
     *
     * @param int $integer An integer to encrypt.
     * @return string The encrypted string.
     */
    public static function  encrypt_id($integer): string
    {
        $data = strval($integer);
        return base64_encode($data);
    }

    /**
     * Decrypts a base64 encoded string into an integer.
     *
     * @param string $encoded The encrypted string.
     * @return string|false The decrypted string or false if the encrypted string is invalid.
     */
    public static function decrypt_id($encoded): false|string
    {
        return base64_decode($encoded);
    }
}
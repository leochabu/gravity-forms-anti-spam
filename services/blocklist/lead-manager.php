<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class LeadManager
{
    public function __construct()
    {
        !defined('TABLENAME_LEAD') && define('TABLENAME_LEAD', 'wp_lead_stats');
    }


    /**
     * Extracts the words from the given entry
     * @param $entry - array with all fields to be compared
     * @return array - array of words from the given entry
     */
    public static function extract_words_from_entry($entry): array
    {
        $input_words = array();
        if (is_array($entry) || is_object($entry)) {
            foreach ($entry as $value) {
                if (is_string($value)) {
                    $words = explode(' ', $value);
                    foreach ($words as $word) {
                        $word = trim($word);
                        if (!empty($word)) {
                            $input_words[] = strtolower($word);
                        }
                    }
                }
            }
        }

        return $input_words;
    }
}
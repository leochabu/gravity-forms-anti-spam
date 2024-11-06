<?php
namespace gfa_services\blocklist;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

include_once "lead-manager.php";
include_once "sanitizer.php";

$redux_core_path = WP_PLUGIN_DIR . '/gravity-forms-anti-spam/vendor/redux-framework/redux-framework/redux-core/framework.php';

if (file_exists($redux_core_path)) {
    require_once $redux_core_path;
}

use Exception;
use Gravity_Anti_Spam;
use LeadManager;
use Redux;
use sanitizer;

global $redux_options;


class BlocklistService
{
    private static $blocklisted_words_lowercase;
    private static $allow_list_words_lowercase;
    private static $blocklisted_tlds_lowercase;
    private static $leads_table;

    public function __construct(){

        /**
         * Here we need to get the list of blocked words from WP Option
         */
        $blocklisted_words = $this->get_blocklist_option( '_blocklisted_terms') ;
        $blocklisted_phones = $this->get_blocklist_option( '_blocklisted_phones');
        $blocklisted_emails = $this->get_blocklist_option( '_blocklisted_emails');

        /**
         * TLDs should be always compared as partial matches.
         */
        $blocklisted_tlds = $this->get_blocklist_option(  '_blocklisted_tlds' );

        if(!empty($blocklisted_emails)) {
            $blocklisted_emails = $blocklisted_emails[0] !== "," ? "," . $blocklisted_emails : $blocklisted_emails;
        }

        if(!empty($blocklisted_phones)) {
            $blocklisted_phones = $blocklisted_phones[0] !== "," ? "," . $blocklisted_phones : $blocklisted_phones;
        }

        //$blocklisted_tlds = $blocklisted_tlds[0] !== "," ? ",".$blocklisted_tlds : $blocklisted_tlds;

        $blocklisted_words .= $blocklisted_emails;
        $blocklisted_words .= $blocklisted_phones;
        $blocklisted_words .= $blocklisted_tlds;


        /**
         * Here we need to get the list of allowed words from WP Option
         */
        $allow_list_words = $this->get_blocklist_option(GFA_OPTIONS . '_allow_list_terms');

        self::$blocklisted_words_lowercase = $this->prepare_lowercase_terms($blocklisted_words);
        self::$allow_list_words_lowercase = $this->prepare_lowercase_terms($allow_list_words);
        self::$blocklisted_tlds_lowercase = $this->prepare_lowercase_terms($blocklisted_tlds);

        self::$leads_table = 'wp_gfa_submission_stats';

    }

    /**
     * Gets a blocklist option from the database.
     *
     * @param string $option_name The name of the option to retrieve.
     *
     * @return string The value of the option, or an empty string if the option does not exist.
     */
    private function get_blocklist_option($option_name): string
    {
        return $this->validate_option( Redux::get_option(GFA_OPTIONS, GFA_OPTIONS . $option_name ) );
    }



    /**
     * Takes a string or array of terms and converts them to an array of lowercase terms.
     *
     * @param string|array $terms The terms to be converted.
     *
     * @return array An array of lowercase terms.
     */
    private function prepare_lowercase_terms($terms): array
    {
        if(is_array($terms) && !empty($terms)){
            $terms = implode(', ', $terms);
        }

        $terms_string = trim(strtolower($terms));
        $terms_array = explode(',',$terms_string);

        return empty($terms_array) ? [] : $terms_array;
    }


    /**
     * Finds for an exact match of the given input string in the blocklisted words
     * @param $entry - array with all fields to be compared
     * @return array - true if the input string is fully blocklisted, false otherwise.
     */
    public static function exact_match($entry): array
    {
        $match_terms = [];

        if (empty($match_terms))
        {
            $input_words = LeadManager::extract_words_from_entry($entry);
            $blocklisted_words = self::$blocklisted_words_lowercase;
            $allow_list_words = self::$allow_list_words_lowercase;

            if (is_array($blocklisted_words)) {
                $blocklist_intersection = array_intersect($input_words, $blocklisted_words);
            }

            if (is_array($allow_list_words)) {
                $allow_list_intersection = array_intersect($input_words, $allow_list_words);
            }

            if (!empty($blocklist_intersection) && empty($allow_list_intersection)) {
                $match_terms = $blocklist_intersection;
            }
        }

        return $match_terms;
    }

    /**
     * Finds for a TLD (Top Level Domain) at the very end of a given string.
     * Example:
     *  - if the TLD .news is in the blocklisted list:
     *  - input string: "user@media.news"
     *  - result = true
     *
     *  - input string: "user.newsome@media.net"
     *  - result = false
     * @param $entry
     * @return array
     * @author Leandro Chaves (@leochabu)
     */
    private static function check_tlds($entry): array
    {
        $blocklisted_tlds = self::$blocklisted_tlds_lowercase;
        $input_array = LeadManager::extract_words_from_entry($entry);
        $input_emails = [];

        foreach ($input_array as $email) {
            foreach ($blocklisted_tlds as $tld) {
                $tld_length = strlen($tld);
                if (substr($email, -$tld_length) === $tld) {
                    $input_emails[] = $email;
                    break;
                }
            }
        }

        return $input_emails;
    }


    /**
     * @return array The array of blocklisted words, in lowercase.
     */
    public static function getblocklisted_words(): array
    {
        return self::$blocklisted_words_lowercase;
    }



    /**
     * Checks if the lead is blocked by languages Chinese or Russian.
     * @param $lead_data
     * @return array
     * @author Leandro Chaves (@leochabu)
     */
    public static function check_language($lead_data): array
    {
        global $redux_options;
        $chineseRegex = '/[\x{4e00}-\x{9fff}]+/u';
        $russianRegex = '/[\p{Cyrillic}]+/u';
        $blocked_by = [];

        $block_chinese = $redux_options[GFA_OPTIONS . '_block_chinese'];
        $block_russian = $redux_options[GFA_OPTIONS . '_block_russian'];

        foreach($lead_data as $field => $value)
        {
            if(!is_array($value))
            {
                if($block_chinese && preg_match($chineseRegex, $value))
                {
                    $blocked_by['language'] = "Chinese language";
                }

                if( $block_russian && preg_match($russianRegex, $value))
                {
                    $blocked_by['language'] =  "Russian language";
                }
            }
        }
        return $blocked_by;
    }


    /**
     * Validate a blocklist option string.
     *
     * This method trims the string and adds a trailing comma if it's not already present.
     * If the option is not a string or is empty, an empty string is returned.
     *
     * @param mixed $option The option to be validated.
     * @return string The validated option string.
     */
    private function validate_option(mixed $option): string
    {
        if(is_string($option) && !empty($option)){
            $terms_string = rtrim($option);
            $terms_string = ltrim($terms_string);
            if (substr($terms_string, -1) !== ',') {
                $terms_string .= ',';
            }
            return $terms_string;
        }

        return '';
    }

}

new BlocklistService();
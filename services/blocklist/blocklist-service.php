<?php
namespace gfa_services\blocklist;
include_once "email-service.php";
include_once "lead-manager.php";
include_once "sanitizer.php";

use Exception;
use Gravity_Anti_Spam;
use LeadManager;
use sanitizer;

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
        $blocklisted_words = $this->get_blocklist_option(GFA_OPTIONS . '_blocklist');
        $blocklisted_phones = $this->get_blocklist_option(GFA_OPTIONS . '_blocklisted_phones');
        $blocklisted_emails = $this->get_blocklist_option(GFA_OPTIONS . '_blocklisted_emails');

        /**
         * TLDs should be always compared as partial matches.
         */
        $blocklisted_tlds = $this->get_blocklist_option(GFA_OPTIONS . '_blocklisted_tlds');

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

    private function get_blocklist_option($option_name)
    {
        return get_option($option_name)
            && strlen(get_option($option_name))
            ? get_option($option_name)
            : "";
    }


    /**
     * @param $terms
     * @return array
     */
    private function prepare_lowercase_terms($terms): array
    {
        if(is_array($terms)){
            $terms_string = implode(', ', $terms);
        }

        $terms_string = trim(strtolower((string)$terms));
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
        $match_terms = self::check_tlds($entry);

        if(empty($match_terms))
        {
            $match_terms = self::check_language($entry);
        }

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
     * Finds for a partial match of the given input string in the blocklisted words
     * @param $entry - string to be compared
     * @return array - true if the input string is partially blocklisted, false otherwise.
     */
    public static function partial_match($entry): array
    {
        $blocklisted_words = self::$blocklisted_words_lowercase;
        $lowercased_input = LeadManager::extract_words_from_entry($entry);

        $blocked_by = [];
        foreach ($blocklisted_words as $blockedWord) {
            foreach ($lowercased_input as $input) {
                if (stripos($input, $blockedWord) !== false) {
                    $blocked_by[] = $blockedWord;
                }
            }
        }

        return $blocked_by;
    }


    /**
     * Get the value of blocklisted_words
     */
    public static function getblocklisted_words(): array
    {
        return self::$blocklisted_words_lowercase;
    }

    /**
     * @return string
     */
    public static function getLeadsTable(): string
    {
        return self::$leads_table;
    }

    public static function set_lead_allow_listed(string $identifier)
    {
       return LeadManager::set_lead_allow_listed($identifier);
    }

    public static function updateLeadFormData(string $identifier, mixed $data)
    {
        return LeadManager ::updateLeadFormData($identifier, $data);
    }


    /**
     * Returns the ID of an entry based on its identifier
     * @param $entry
     * @return int
     */
    private static function get_lead_stats_id_from_entry($entry): int
    {
        global $wpdb;
        $table_name = self::$leads_table;
        $lead_identifier = $entry['entry']['identifier'];

        $lead_id_query = $wpdb->prepare(
            "SELECT id FROM $table_name WHERE form_data LIKE %s",
            '%' . $wpdb->esc_like($lead_identifier) . '%');

        $lead_id = $wpdb->get_var($lead_id_query);

        return intval($lead_id);
    }

    /**
     * Marks the given entry as blocked
     * @param $entry - array with all fields
     * @return void - marks the given entry as blocked
     */

    private static function mark_as_blocked($entry, $blocked_by)
    {
        $entry = unserialize($entry);
        $entry['entry']["is_blocked"] = $blocked_by;

        $marked_entry = serialize($entry);

        self::update_entry_form_data($entry, $marked_entry);
    }

    /**
     * Updates the form data of the given entry with the given marked entry
     * @param $entry
     * @param $marked_entry
     * @return void
     */
    private static function update_entry_form_data($entry, $marked_entry)
    {

        global $wpdb;

        $entry_id = self::get_lead_stats_id_from_entry($entry);

        $sanitized_marked_entry = $wpdb->esc_like($marked_entry);  // Sanitize user input

        $wpdb->update(
            self::$leads_table,
            array(
                'form_data' => $wpdb->prepare('%s', $sanitized_marked_entry), // Use prepared statement
            ),
            array('id' => $entry_id),
            array('%s')
        );
    }




    /**
     * Checks if the lead is blocked by languages Chinese or Russian.
     * @param $lead_data
     * @return array
     * @author Leandro Chaves (@leochabu)
     */
    public static function check_language($lead_data): array
    {
        $chineseRegex = '/[\x{4e00}-\x{9fff}]+/u';
        $russianRegex = '/[\p{Cyrillic}]+/u';
        $blocked_by = [];

        $block_chinese = (bool)get_option(PLUGIN_NAME . '_block_chinese');
        $block_russian = (bool)get_option(PLUGIN_NAME . '_block_russian');

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

    public static function send_list_of_blocked_leads(): void{
        EmailService::send_list_of_blocked_leads();
    }

}
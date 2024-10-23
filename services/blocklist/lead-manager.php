<?php

class LeadManager
{
    public function __construct()
    {
        !defined('TABLENAME_LEAD') && define('TABLENAME_LEAD', 'wp_lead_stats');
    }

    /**
     * Updates the list of blocked leads in the database
     * @param $data
     * @return void
     * @author Leandro Chaves (@leochabu)
     */
    public static function setNotifiedOnError($data): void
    {
        $data = unserialize($data);
        $identifier = $data['entry']['identifier']; //isset($data['identifier']) ? $data['identifier'] : "" ;

        if (!empty($identifier)) {
            global $wpdb;

            $table_name = TABLENAME_LEAD;
            $identifier = '%' . $wpdb->esc_like($identifier) . '%';

            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $table_name SET notified_on_error = 1 WHERE form_data LIKE %s",
                    $identifier
                )
            );
        }
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

    /**
     * @param array $leadData
     * @return void
     * @author Leandro Chaves (@leochabu)
     */
    public static function register_submission(array $leadData): void
    {
        global $wpdb;
        $leadData['identifier'] = uniqid();
        $blocklisted = isset($leadData['blocklisted']) ? $leadData['blocklisted'] : null;
        $serialized_data = serialize($leadData);
        $wpdb->insert(
            TABLENAME_LEAD,
            array(
                'form_data' => $serialized_data,
                'blocklisted' =>  $blocklisted,
            ));
    }

    /**
     * Returns a list of blocked leads based on the blocked_by key on form_data
     * @return array
     */
    public static function get_blocked_leads(): array
    {
        global $wpdb;

        $blocked_leads_query = $wpdb->prepare(
            "
              SELECT form_data
              FROM wp_lead_stats
              WHERE form_data LIKE %s
              AND created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
              AND notified_on_error IS NULL
              ORDER BY created_at DESC
              ",
            '%' . $wpdb->esc_like('blocked_by') . '%'
        );

        $results = $wpdb->get_results($blocked_leads_query);

        return $results;
    }

    /**
     * Returns a list of blocked leads based on the blocked_by key on form_data
     * @return array
     */
    public static function get_blocked_lead_by_id($identifier): array
    {
        global $wpdb;

        $blocked_leads_query = $wpdb->prepare(
            "
              SELECT form_data
              FROM wp_lead_stats
              WHERE form_data LIKE %s
              LIMIT 1
              ",
            '%' . $wpdb->esc_like($identifier) . '%'
        );

        $results = $wpdb->get_results($blocked_leads_query);

        return $results;
    }

    public static function set_lead_allow_listed(string $identifier)
    {
        $data = self::get_blocked_lead_by_id($identifier);
        if(empty($data)){
            return;
        }

        $data = unserialize($data[0]->form_data);

        if (isset($data['entry']['blocked_by'])) {
            unset($data['entry']['blocked_by']);
        }

        $data['entry']['allowlisted'] = true;

        return $data;

    }

    public static function updateLeadFormData(string $identifier, array $data)
    {
        $data = serialize($data);

        //$data = unserialize($data);

        if (!empty($identifier)) {
            global $wpdb;

            $table_name = TABLENAME_LEAD;
            $identifier = '%' . $wpdb->esc_like($identifier) . '%';

            $result = $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $table_name SET form_data = '$data', notified_on_error = NULL, submission_attempts = 1  WHERE form_data LIKE %s",
                    $identifier
                )
            );

            if ($result === false) {
                error_log("Error updating lead data: " . $wpdb->last_error);
            }

            return $result;

        }

        return false;
    }
}
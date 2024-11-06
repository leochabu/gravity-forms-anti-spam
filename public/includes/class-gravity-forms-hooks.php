<?php
use gfa_services\blocklist\BlocklistService;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Class_Gravity_Forms_Hooks
{
    private static ?Class_Gravity_Forms_Hooks $_instance = null;

    private function __construct() {}

    public static function getInstance(): Class_Gravity_Forms_Hooks
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __clone() {}
    public function __wakeup() {}

    /**
     * Define hooks for the add-on.
     *
     * Adds a filter to the `gform_entry_is_spam` filter. This filter is used to check if a form entry
     * is spam or not. The callback method is {@see Class_Gravity_Forms_Hooks::gfa_check_if_spam()}.
     *
     * @since 1.0.0
     */
    public function define_hooks(): void
    {
        add_filter( 'gform_entry_is_spam', [ $this, 'gfa_check_if_spam' ], 10, 3 );
        //add_action( 'gform_pre_submission', array( $this, 'gfa_pre_submission_check' ) );
        //add_action( 'gform_after_submission', [ $this, 'gfa_after_submission_check' ], 10, 2 );
    }

    /**
     * Check if the given form entry is considered spam.
     *
     * Hooked to `gform_entry_is_spam` filter.
     *
     * @param bool    $is_spam  Whether the entry is marked as spam.
     * @param array   $form     The form settings.
     * @param array   $entry    The form entry.
     * @return bool   True if the entry is marked as spam, false otherwise.
     */
    public function gfa_check_if_spam( $is_spam, $form, $entry ): bool
    {
       if( !$this->gfa_form_use_anti_spam( $form ) ) {
           return $is_spam;
       }

        $match_terms = BlocklistService::exact_match( $entry );

        if ( ! empty( $match_terms ) ) {
            $is_spam = true;
        }

        return $is_spam;
    }




    /**
     * Checks if the given form entry is considered spam before submission.
     *
     * Hooked to `gform_pre_submission` action.
     *
     * @param array $form The form settings.
     */
    public function gfa_pre_submission_check($form): void
    {
        if( !$this->gfa_form_use_anti_spam( $form ) ) {
            return;
        }

        $entry = GFFormsModel::create_lead($form);

        $bls = new BlocklistService();
        $match_terms =$bls::exact_match($entry);

        if (!empty($match_terms)) {
            GFAPI::update_entry_property($entry['id'], 'is_spam', 1);
        }
    }

    /**
     * Checks if the given form entry is considered spam after submission.
     *
     * Hooked to `gform_after_submission` action.
     *
     * @param array $entry The form entry.
     * @param array $form  The form settings.
     */
    public function gfa_after_submission_check($entry, $form): void
    {
        if (!isset($form['use_anti_spam']) || !$form['use_anti_spam']) {
            return;
        }

        $match_terms = BlocklistService::exact_match($entry);

        if (!empty($match_terms)) {
            GFAPI::update_entry_property($entry['id'], 'is_spam', 1);
        }
    }

    /**
     * Checks if the given form is configured to use anti-spam protection.
     *
     * @param array $form The form object.
     * @return bool True if the form is configured to use anti-spam protection, false otherwise.
     */
    public function gfa_form_use_anti_spam( $form ): bool
    {
        if ( ! isset( $form['gravity-anti-spam']['use_anti_spam'] ) || $form['gravity-anti-spam']['use_anti_spam'] != 1 ) {
            return false;
        }

        return true;
    }

}

$instance = Class_Gravity_Forms_Hooks::getInstance();
$instance->define_hooks();
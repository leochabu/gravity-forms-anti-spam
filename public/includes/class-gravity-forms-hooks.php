<?php
use gfa_services\blocklist\BlocklistService;

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




    public function gfa_pre_submission_check($form): void
    {
        if( !$this->gfa_form_use_anti_spam( $form ) ) {
            return;
        }

        $entry = GFFormsModel::create_lead($form);

        echo("<pre> entry");
        print_r($entry);
        echo("</pre>");

        $bls = new BlocklistService();
        $match_terms =$bls::exact_match($entry);

        var_dump($match_terms);

        echo("</pre>");

        if (!empty($match_terms)) {
            GFAPI::update_entry_property($entry['id'], 'is_spam', 1);
        }
    }

    public function gfa_after_submission_check($entry, $form): void
    {
        var_dump("after submission");
        if (!isset($form['use_anti_spam']) || !$form['use_anti_spam']) {
            return;
        }

        $match_terms = BlocklistService::exact_match($entry);

        echo("<pre> match terms");
        print_r($match_terms);
        echo("</pre>");

        echo("<pre> entry");
        print_r($entry);
        echo("</pre>");

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
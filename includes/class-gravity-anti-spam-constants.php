<?php
class Gravity_Anti_Spam_Constants
{
    public static function check_constants(): void
    {
        !defined('GFA_PREFIX' ) && define( 'GFA_PREFIX', 'GFA' );


        global $wpdb;
        !defined(GFA_PREFIX . '_SUBMISSION_TABLE' ) && define( GFA_PREFIX . '_SUBMISSION_TABLE', $wpdb->prefix . 'gfa_submission_stats' );

        /**
         * Currently plugin version.
         */
        !defined('GRAVITY_ANTI_SPAM_VERSION' ) && define( 'GRAVITY_ANTI_SPAM_VERSION', '1.0.0' );



        !defined(GFA_PREFIX . '_PLUGIN_NAME' ) && define( GFA_PREFIX . '_PLUGIN_NAME', 'Gravity Anti Spam' );
        !defined(GFA_PREFIX . '_PLUGIN_SLUG' ) && define( GFA_PREFIX . '_PLUGIN_SLUG', 'gravity-anti-spam' );
        !defined(GFA_PREFIX . '_OPTIONS' ) && define(GFA_PREFIX . '_OPTIONS', 'gfa' );
        !defined(GFA_PREFIX . '_TEXT_DOMAIN' ) && define( GFA_PREFIX . '_TEXT_DOMAIN', 'gravity-anti-spam' );
    }
}

Gravity_Anti_Spam_Constants::check_constants();
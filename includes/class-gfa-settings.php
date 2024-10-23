<?php

/**
 * User: leochabu
 * Date: 19/10/2024
 * Time: 17:35
 */

namespace gf_anti_spam;
class GFA_Settings
{

    public static function register_settings(): void
    {
        add_option(GFA_OPTIONS . '_blocklist');
        add_option(GFA_OPTIONS . '_blocklisted_words');
        add_option(GFA_OPTIONS . '_blocklisted_emails');
        add_option(GFA_OPTIONS . '_blocklisted_tlds');
        add_option(GFA_OPTIONS . '_blocklisted_phones');
        add_option(GFA_OPTIONS . '_whitelisted_terms');
        add_option(GFA_OPTIONS . '_use_partial_match');
        add_option(GFA_OPTIONS . '_blocklisted_notified_users');

        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_blocklist');
        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_blocklisted_words');
        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_blocklisted_emails');
        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_blocklisted_tlds');
        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_blocklisted_phones');
        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_whitelisted_terms');
        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_use_partial_match');
        register_setting(GFA_OPTIONS . '_options', GFA_OPTIONS . '_blocklisted_notified_users');

    }
}
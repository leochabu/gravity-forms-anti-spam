<?php
//var_dump( "Partials");
if (!class_exists('Redux')) {
    return;
}
$opt_name = GFA_OPTIONS;

$args = array(
    'display_name'         => 'Anti Spam Settings',
    'display_version'      => GRAVITY_ANTI_SPAM_VERSION,
    'menu_title'           => esc_html__( 'Anti Spam Settings', GFA_TEXT_DOMAIN ),
    'page_title'           => esc_html__( 'Anti Spam Settings', GFA_TEXT_DOMAIN ),
    'menu_type'            => 'menu',
    'menu_slug'            => 'gfa-settings',
    'customizer'           => false,
    'menu_icon'            => 'dashicons-welcome-comments',
    'dev_mode'             => false,
);

Redux::set_args( $opt_name, $args );



Redux::set_field( $opt_name, 'general', array(
    'id'       => GFA_OPTIONS . '_blocklisted_tlds',
    'type'     => 'textarea',
    'title'    => esc_html__( 'Block list of TLDs', GFA_TEXT_DOMAIN ),
    'subtitle' => esc_html__( 'Block Top-Level Domains (TLDs) e.g., "top.com".', GFA_TEXT_DOMAIN ),
    'desc'     => esc_html__( 'Please add the TLDs separated by commas (,)', GFA_TEXT_DOMAIN ),
    'default'  => 'top.com, block.net'
) );

Redux::set_field( $opt_name, 'general', array(
    'id'       => GFA_OPTIONS . '_blocklisted_terms',
    'type'     => 'textarea',
    'title'    => esc_html__( 'Block list of terms', GFA_TEXT_DOMAIN ),
    'subtitle' => esc_html__( 'Block all terms in the list.', GFA_TEXT_DOMAIN ),
    'desc'     => esc_html__( 'Please add the terms separated by commas (,)', GFA_TEXT_DOMAIN ),
    'default'  => 'crypto, seo, bitcoin'
) );

Redux::set_section(
    $opt_name,
    array(
        'title'  => esc_html__( 'General Settings', GFA_TEXT_DOMAIN ),
        'id'     => 'general',
        'desc'   => esc_html__( 'General settings for this plugin.', GFA_TEXT_DOMAIN ),
        'icon'   => 'el el-home',
    )
);


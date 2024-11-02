<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

GFForms::include_addon_framework();

class GF_Anti_Spam_Addon extends GFAddOn {

    protected $_version = '1.0.0';
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'gravity-anti-spam';
    protected $_path = 'gravity-anti-spam-addon/gravity-anti-spam-addon.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Gravity Anti Spam Add-On';
    protected $_short_title = 'Anti Spam';

    private static $_instance = null;

    /**
     * Get an instance of this class.
     *
     * @return GF_Anti_Spam_Addon
     */
    public static function get_instance() {
        if ( self::$_instance === null ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Register the settings for the form editor sidebar.
     *
     * @param array $form The current form
     */
    public function form_settings_fields( $form ) {
        return [
            [
                'title'  => esc_html__( 'Anti Spam Settings', 'gravity-anti-spam' ),
                'fields' => [
                    [
                        'label'   => esc_html__( 'Enable Anti Spam', 'gravity-anti-spam' ),
                        'type'    => 'checkbox',
                        'name'    => 'use_anti_spam',
                        'tooltip' => esc_html__( 'Activate anti-spam protection for this form', 'gravity-anti-spam' ),
                        'choices' => [
                            [
                                'label' => esc_html__( 'Use Anti Spam', 'gravity-anti-spam' ),
                                'name'  => 'use_anti_spam',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

// Initialize the add-on
function gfa_initialize_anti_spam_addon() {
    if ( class_exists( 'GFForms' ) ) {
        GFAddOn::register( 'GF_Anti_Spam_Addon' );
    }
}
add_action( 'gform_loaded', 'gfa_initialize_anti_spam_addon', 5 );

add_filter( 'gform_pre_form_settings_save', function( $form ) {
    $form['use_anti_spam'] = isset( $_POST['use_anti_spam'] ) ? 1 : 0;
    return $form;
});

// Helper function to get the instance
function gf_anti_spam() {
    return GF_Anti_Spam_Addon::get_instance();
}


<?php

$admin_hooks = WP_PLUGIN_DIR . '/gravity-forms-anti-spam/includes/class-gravity-anti-spam-addon.php';

if (file_exists($admin_hooks)) {
    require_once $admin_hooks;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/leochabu
 * @since      1.0.0
 *
 * @package    Gravity_Anti_Spam
 * @subpackage Gravity_Anti_Spam/admin
 */


/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gravity_Anti_Spam
 * @subpackage Gravity_Anti_Spam/admin
 * @author     Leandro Chaves <leochabu@gmail.com>
 */
class Gravity_Anti_Spam_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action( 'plugins_loaded', array( $this, 'admin_options_page' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gravity_Anti_Spam_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gravity_Anti_Spam_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gravity-anti-spam-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gravity_Anti_Spam_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gravity_Anti_Spam_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gravity-anti-spam-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Admin menu
     *
     * @since    1.0.0
     */
    public function admin_options_page() {

        if(is_admin()){
            include "partials/general_settings.php";
        }
    }

}


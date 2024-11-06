<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/leochabu
 * @since             1.0.0
 * @package           Gravity_Anti_Spam
 *
 * @wordpress-plugin
 * Plugin Name:       Gravity Forms Anti-Spam
 * Plugin URI:        https://github.com/leochabu/gravity-forms-anti-spam
 * Description:       This plugin allows you to decide which terms will block a submission, drastically reducing the amount of SPAM.
 * Version:           1.0.2
 * Author:            Leandro Chaves
 * Author URI:        https://github.com/leochabu/
 * License:           License: GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gravity-anti-spam
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/includes/class-gravity-anti-spam-constants.php';
require_once __DIR__ . '/services/blocklist/blocklist-service.php';

$redux_core_path = WP_PLUGIN_DIR . '/gravity-forms-anti-spam/vendor/redux-framework/redux-framework/redux-core/framework.php';

if (file_exists($redux_core_path)) {
    require_once $redux_core_path;
}

if (!class_exists('ReduxFramework')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p><strong>Redux Framework is not installed!</strong></p></div>';
    });
    return;
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gravity-anti-spam-activator.php
 */
function activate_gravity_anti_spam() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gravity-anti-spam-activator.php';

    if (!class_exists('GFAPI')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            __('This plugin requires Gravity Forms to be installed. Please install Gravity Forms and try again.', 'gravity-anti-spam'),
            __('Activation Error', GFA_TEXT_DOMAIN),
            array('back_link' => true)
        );
    }

    Gravity_Anti_Spam_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gravity-anti-spam-deactivator.php
 */
function deactivate_gravity_anti_spam() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gravity-anti-spam-deactivator.php';
	Gravity_Anti_Spam_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gravity_anti_spam' );
register_deactivation_hook( __FILE__, 'deactivate_gravity_anti_spam' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gravity-anti-spam.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gravity_anti_spam() {
    $plugin = new Gravity_Anti_Spam();
	$plugin->run();
}


run_gravity_anti_spam();
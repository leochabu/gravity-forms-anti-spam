<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/leochabu
 * @since      1.0.0
 *
 * @package    Gravity_Anti_Spam
 * @subpackage Gravity_Anti_Spam/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gravity_Anti_Spam
 * @subpackage Gravity_Anti_Spam/includes
 * @author     Leandro Chaves <leochabu@gmail.com>
 */
class Gravity_Anti_Spam_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gravity-anti-spam',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

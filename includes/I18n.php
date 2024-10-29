<?php

/**
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
namespace StorePlugin\CustomOrderStatus;

use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * Define the internationalization functionality.
 *
 * @since      1.0.0
 * @package    StorePlugin\CustomOrderStatus
 * @author     StorePlugin <contact@storeplugin.net>
 */
class I18n implements ServiceInterface {

	public function register(): void {
		$this->load_plugin_textdomain();
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'advanced-custom-order-status-for-woocommerce', false, SPCOS_ORDER_STATUS_DIR . '/languages/' );
	}

}

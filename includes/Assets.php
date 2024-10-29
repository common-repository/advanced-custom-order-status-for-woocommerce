<?php

/**
 * The file that defines actions on plugin deactivation.
 *
 * @package StorePlugin\CustomOrderStatus
 */
namespace StorePlugin\CustomOrderStatus;

use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * Enqueue assets for custom order status plugin
 */
class Assets implements ServiceInterface {

	public function register(): void
	{
		\add_action( 'wp_enqueue_scripts', [$this, 'frontend_assets'] );
		\add_action( 'admin_enqueue_scripts', [$this, 'admin_assetes'] );
	}

    /**
	 * Enqueue assets for frontend area
	 *
	 * @since    1.0.0
	 */
	public function frontend_assets() {}

	/**
	 * Enqueue assets for admin area
	 *
	 * @since    1.0.0
	 */
	public function admin_assetes() {
		wp_enqueue_style( 'custom-order-status-admin-style', SPCOS_ORDER_STATUS_URI . '/css/admin-style.css', array(), '2.0.0', 'all' );
	}

}

<?php

/**
 * Plugin Name:       Advanced Custom Order Status for WooCommerce
 * Requires Plugins:  woocommerce
 * Plugin URI:        https://storeplugin.net/plugins/advanced-custom-order-status-for-woocommerce
 * Description:       The Advanced Custom Order Status for WooCommerce plugin empowers users to effortlessly manage and customize Order Status in WooCommerce.
 * Version:           2.1.0
 * Author:            StorePlugin
 * Author URI:        https://storeplugin.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       advanced-custom-order-status-for-woocommerce
 * Domain Path:       /languages
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/** PSR-4 compatible autoload */
$_autoload = require __DIR__ . '/vendor/autoload.php';

/** Import necessary classes */
use StorePlugin\CustomOrderStatus\Container;
use StorePlugin\CustomOrderStatus\PluginFactory;
use StorePlugin\CustomOrderStatus\Container\Dice;
use StorePlugin\CustomOrderStatus\Utils;

/** Plugin PATH constant */
if( ! defined( 'SPCOS_ORDER_STATUS_FILE' ) ) {
    define( 'SPCOS_ORDER_STATUS_FILE', __FILE__ );
}

/** Plugin DIR constant */
if( ! defined( 'SPCOS_ORDER_STATUS_DIR' ) ) {
    define( 'SPCOS_ORDER_STATUS_DIR', __DIR__ );
}

/** Plugin Base constant */
if( ! defined( 'SPCOS_ORDER_STATUS_BASE' ) ) {
    define( 'SPCOS_ORDER_STATUS_BASE', plugin_basename( SPCOS_ORDER_STATUS_FILE ) );
}

/** Plugin URI constant */
if( ! defined( 'SPCOS_ORDER_STATUS_URI' ) ) {
    define( 'SPCOS_ORDER_STATUS_URI', plugins_url( '/assets', SPCOS_ORDER_STATUS_FILE ) );
}

/** Plugin version constant */
if( ! defined( 'SPCOS_ORDER_STATUS_VERSION' ) ) {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugin_data = get_plugin_data( SPCOS_ORDER_STATUS_FILE );
    define( 'SPCOS_ORDER_STATUS_VERSION', $plugin_data['Version'] );
}

/**
 * Deactivate the unsupported Pro version
 * During the initial lookup of the stpwcos_order_attribute() function.
 *
 * Will be removed in the future version
 *
 * @since 2.0.0
 * @return void
 */
if( ! function_exists( 'stpwcos_order_attribute' ) ) {
    function stpwcos_order_attribute( $arg ) {
        try {
            Utils::deactivate_order_status_pro();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return;
    }
}

/**
 * Deactivate the unsupported Pro version
 * During the initial lookup of the stpwcos_order_attributes() function.
 *
 * Will be removed in the future version
 *
 * @since 2.0.0
 * @return array[]
 */
if ( ! function_exists('stpwcos_order_attributes') ) {
    function stpwcos_order_attributes() {
        try {
            Utils::deactivate_order_status_pro();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return [];
    }
}

/** Load container with autowiring process. */
add_action( 'plugins_loaded', function() use( $_autoload ) {
    if( class_exists( Container::class ) ) {
        if( Utils::is_active() ) {
            // Boot Starter plugin
            ( new Container( $_autoload->getPrefixesPsr4(), 'StorePlugin\CustomOrderStatus' ) )
                ->container( new Dice() )
                ->register();

			// Check WooCommerce HPOS
			add_action( 'before_woocommerce_init', array( Utils::class, 'declare_hpos_compatibility' ) );
        }
    }
});

/** Do things in plugin activation and deactivation */
add_action( 'init', fn() => Utils::deactivate_order_status_pro() );
add_action( 'admin_footer', fn() =>  Utils::disable_order_status_active_link() );
register_activation_hook( SPCOS_ORDER_STATUS_FILE, fn() => PluginFactory::activate() );
register_deactivation_hook( SPCOS_ORDER_STATUS_FILE, fn() => PluginFactory::deactivate() );
add_filter( 'plugin_action_links_' . SPCOS_ORDER_STATUS_BASE, [ Utils::class, 'setting_link_next_plugin_activation'] );

/* 2.0.0 PRO Notice - Will be removed in the future version */
add_action( 'admin_notices',  array( Utils::class, 'update_notice_for_pro_plugin'));
add_action( 'after_plugin_row_advanced-custom-order-status-pro-for-woocommerce/advanced-custom-order-status-pro-for-woocommerce.php', array( Utils::class, 'manual_update_notice_for_pro_plugin'), 10, 2 );

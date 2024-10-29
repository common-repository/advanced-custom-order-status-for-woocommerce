<?php

/**
 * Utils class contain common utility and most used methods.
 *
 * @package StorePlugin\CustomOrderStatus
 */
namespace StorePlugin\CustomOrderStatus;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

class Utils {

	/**
	 * Check if the premium plugin exists in the database
	 *
	 * @return string
	 */
	private static function get_pro_plugin_path() {
		$plugin_name = 'advanced-custom-order-status-pro-for-woocommerce';
		return trailingslashit( WP_PLUGIN_DIR ) . "{$plugin_name}/{$plugin_name}.php";
	}

	/**
	 * Get the version of the premium custom order status plugin
	 *
	 * @return string
	 */
	private static function get_pro_plugin_version() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( file_exists( self::get_pro_plugin_path() ) ) {
			$pro_version = get_plugin_data( self::get_pro_plugin_path() );
			return $pro_version[ 'Version' ];
		} else {
			return '0.0.0';
		}
	}

    /**
	 * Check if the WooCommerc is active
	 *
	 * @return bool
	 */
	public static function is_active() {
        if( ! function_exists( 'WC' ) ) {
            add_action( 'admin_notices', [ __CLASS__, 'admin_notice_missing_woocommerce' ] );
			return false;
        }

		// Check if Pro is active.
		if ( in_array( self::get_pro_plugin_path(), wp_get_active_and_valid_plugins() ) ) {
			return false;
		}

		return true;
	}

    /**
	 * Admin notice
	 *
	 * Warning when the site doesn't have WooCommerce installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function admin_notice_missing_woocommerce() {
        // PHPCS:Ignore
		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'advanced-custom-order-status-for-woocommerce' ),
			'<strong>' . esc_html__( 'Advanced Custom Order Status for WooCommerce', 'advanced-custom-order-status-for-woocommerce' ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'advanced-custom-order-status-for-woocommerce' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );

	}

    /**
	 * Declare compatibility with WooCommerce HPOS
	 *
	 * @return void
	 */
	public static function declare_hpos_compatibility() {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'advanced-custom-order-status-for-woocommerce', SPCOS_ORDER_STATUS_FILE, true );
		}
	}

    /**
	 * Setting page link from plugin page.
	 *
	 * @param array $links
	 * @return array
	 */
	public static function setting_link_next_plugin_activation( $links ) {
		$links[] = '<a href="' . admin_url( 'edit.php?post_type=wc_order_status' ) . '">Settings</a>';
		if( Utils::is_active() ) {
			$links[] = '<a href="' . admin_url( 'edit.php?post_type=wc_order_email' ) . '">Email Settings (Pro)</a>';
			$links[] = '<a href="https://storeplugin.net/plugins/advanced-custom-order-status-for-woocommerce/?utm_source=activesite&utm_campaign=corder&utm_medium=link" target="_blank">Get Pro</a>';
		}
		return $links;
	}

    /**
     * Get all order statuses data
     *
     * @return WP_Post[]|int[]
     */
    public static function get_order_attributes() {
        $order_attributes = get_posts(array(
            'post_type'            => 'wc_order_status',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
        ));

        return $order_attributes;
    }

    /**
     * Get specific order statuses data
     *
     * @return WP_Post[]|int[]
     */
    public static function get_order_attribute($meta_key) {
        $order_attribute = get_posts(array(
            'post_type'         => 'wc_order_status',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                array(
                    'key'       => $meta_key,
                    'value'     => '',
                    'compare'   => '!='
                ),
            ),
        ));

        return $order_attribute;
    }

	/**
	 * Deactivate the version lower than 2.0.0 of the Order Status Pro plugin
	 *
	 * @return void
	 */
	public static function deactivate_order_status_pro() {
		if( self::get_pro_plugin_version() < '2.0.0' ) {
			deactivate_plugins( self::get_pro_plugin_path(), true );
		}
	}

	/**
	 * Disable activate link until the user updates the plugin to version 2.0.0 or later
	 *
	 * @return void
	 */
	public static function disable_order_status_active_link() {
		if( self::get_pro_plugin_version() < '2.0.0' ) {
		?>
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					var orderStatusId = document.getElementById('activate-advanced-custom-order-status-pro-for-woocommerce');
					orderStatusId.removeAttribute('href');
					orderStatusId.style.pointerEvents = 'none';
					orderStatusId.style.color = 'gray';
				});
			</script>
		<?php
		}
	}

	/**
	 * Plugin page PRO update notice
	 *
	 * Will be removed in the future version
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function update_notice_for_pro_plugin() {
		if ( (self::get_pro_plugin_version() != '0.0.0') && version_compare( self::get_pro_plugin_version(), '2.0.0', '<' ) ) {
		?>
			<div class="notice notice-error">
				<p><?php echo sprintf(
    				__( 'You are using an outdated version of <strong>"Advanced Custom Order Status Pro for WooCommerce"</strong>, which limit functionality. Please update to the latest version from your account at %s to access all features.', 'advanced-custom-order-status-for-woocommerce' ),
    				'<a href="https://storeplugin.net/account/" target="_blank">storeplugin.net</a>'
				); ?></p>
			</div>
		<?php
		}
	}

	/**
	 * Plugin page PRO update notice under plugin name
	 *
	 * Will be removed in the future version
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function manual_update_notice_for_pro_plugin( $file, $plugin_data ) {
		if ( strpos( $file, 'advanced-custom-order-status-pro-for-woocommerce.php' ) !== false && (self::get_pro_plugin_version() != '0.0.0') && version_compare( self::get_pro_plugin_version(), '2.0.0', '<' ) ) {
			echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange">
					<div class="update-message notice inline notice-warning notice-alt">
						<p>There is a new version of Advanced Custom Order Status Pro for WooCommerce. <a href="https://storeplugin.net/account/" target="_blank">View version 2.0.0 details</a> <em>Automatic update is unavailable for this plugin</em></p>
					</div>
				</td></tr>';
		}
	}

}

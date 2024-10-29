<?php

/**
 * The WooCommerce Settings functionality for custom order status
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\Settings
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\Settings;

use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * AbstractWcSettings
 *
 * AbstractWcSettings holds WooCommerce settings functionalities to create tabs and sections.
 */
class WcSettings implements ServiceInterface {

    public function register(): void {
        \add_action( 'woocommerce_settings_tabs', [$this, 'orderStatusSettingsTab'], 1 );
        \add_action( 'all_admin_notices', [$this, 'wcSettingsTabPostType'], 5 );
        \add_action( 'all_admin_notices', [$this, 'wcSettingsSection'] );
        \add_filter( 'parent_file', [$this, 'settingsMenuHighlight'] );
    }

	/**
	 * Check post type param
	 *
	 * @param string $post_type_name
	 * @return bool
	 */
	protected function is_post_type( $post_type_name ) {
		global $typenow;
		return $typenow === $post_type_name;
	}

	/**
	 * Check the current screen of order status post
	 *
	 * @return bool
	 */
	protected function is_order_status_screen() {
		if ( ! function_exists( 'get_current_screen') ) return false;

		$screen = get_current_screen();
		return $screen && in_array( $screen->id, array(
			'wc_order_status',
			'edit-wc_order_status',
			'wc_order_email',
			'edit-wc_order_email',
		), true );
	}

    /**
	 * Create tab in WooCommerce settings
	 *
	 * @return void
	 */
	public function orderStatusSettingsTab() {
        echo '<a href="', esc_url( admin_url( 'edit.php?post_type=wc_order_status' ) ) ,'" class="nav-tab ', ( $this->is_post_type( 'wc_order_status' ) || $this->is_post_type( 'wc_order_email' ) ? 'nav-tab-active' : '' ) ,'">', esc_html__( 'Order Statuses', 'advanced-custom-order-status-for-woocommerce' ) ,'</a>';

    }

    /**
	 * Display all WooCommerce settings tabs on top of custom order post type.
	 *
	 * @return void
	 */
	public function wcSettingsTabPostType() {
		if( ! $this->is_order_status_screen() ) return;

		// Get settings page
		\WC_Admin_Settings::get_settings_pages();
		$wc_tabs = apply_filters('woocommerce_settings_tabs_array', array());

		?>
		<div class="wrap woocommerce">
			<nav class="nav-tab-wrapper wc-nav-tab-wrapper">
				<?php foreach ( $wc_tabs as $name => $label ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . $name ) ); ?>" class="nav-tab"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
				<?php do_action( 'woocommerce_settings_tabs' ); ?>
			</nav>
		</div>
		<?php
	}

    /**
	 * Output section in order status tab.
	 *
	 * @uses $current_section
	 */
	public function wcSettingsSection() {
		if( ! $this->is_order_status_screen() ) return;
	?>
		<ul class="subsubsub">
			<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_order_status' ) ); ?>" class="<?php echo ( $this->is_post_type( 'wc_order_status' ) ? 'current' : '' ); ?>"><?php esc_html_e( 'Statuses', 'advanced-custom-order-status-for-woocommerce' ); ?></a> | </li>
			<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_order_email' ) ); ?>" class="<?php echo ( $this->is_post_type( 'wc_order_email' ) ? 'current' : '' ); ?>"><?php esc_html_e( 'Emails', 'advanced-custom-order-status-for-woocommerce' ); ?></a></li>
		</ul>
		<div class="clear"></div>
	<?php
	}

    /**
	 * Highlight WooCommerce settings menu
	 *
	 * @param string $parent_file
	 * @return string
	 */
	public function settingsMenuHighlight( $parent_file ) {
		global $submenu_file;

		if ( $this->is_order_status_screen() ) {
			$parent_file  = 'woocommerce';
			$submenu_file = 'wc-settings';
		}

		return $parent_file;
	}

}

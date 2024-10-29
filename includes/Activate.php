<?php

/**
 * The file that defines actions on plugin activation.
 *
 * @package StorePlugin\CustomOrderStatus
 */
namespace StorePlugin\CustomOrderStatus;

/**
 * The plugin activation class.
 *
 * @since 1.0.0
 */
class Activate
{
	/**
	 * Default color for default order status
	 *
	 * @param string $_color
	 * @return string|void
	 */
	private function color( $_color ) {
		switch( $_color ) {
			case 'processing':
				return '#5b841b';
			break;
			case 'pending':
			case 'refunded':
			case 'cancelled':
			case 'checkout-draft':
				return '#777';
			break;
			case 'on-hold':
				return '#94660c';
			break;
			case 'completed':
				return '#2e4453';
			case 'failed':
				return '#761919';
			break;

		}
	}

	/**
	 * Default background color for default order status
	 *
	 * @param string $_bgColor
	 * @return string|void
	 */
	private function bgColor( $_bgColor ) {
		switch( $_bgColor ) {
			case 'processing':
				return '#c6e1c6';
			break;
			case 'pending':
			case 'refunded':
			case 'cancelled':
			case 'checkout-draft':
				return '#e5e5e5';
			break;
			case 'on-hold':
				return '#f8dda7';
			break;
			case 'completed':
				return '#c8d7e1';
			case 'failed':
				return '#eba3a3';
			break;
		}
	}

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate(): void
	{

		// Check if order status exists in database.
		$orderStatusExist = \StorePlugin\CustomOrderStatus\Utils::get_order_attributes();
		if( ! empty( $orderStatusExist ) ) return;

		// Get all order statuses
		$orderStatuses = wc_get_order_statuses();

		// Add default order status on plugin activation
		if( is_array( $orderStatuses ) ) {
			foreach( $orderStatuses as $status => $title ) {
				$_slug 	  = str_replace('wc-','', $status);
				$_color   = $this->color( $_slug );
				$_bgColor = $this->bgColor( $_slug );

				wp_insert_post(
					array(
						'post_title' 				=> $title,
						'post_name'					=> $status,
						'post_type'					=> 'wc_order_status',
						'post_status'				=> 'publish',
						'meta_input'				=> array(
							'_title_status'			=> $title,
							'_slug_status'			=> $_slug,
							'_order_status_default'	=> 'yes',
							'_color_status'			=> $_color,
							'_bg_color_status'		=> $_bgColor,
							'_order_status_type' 	=> ( 'processing' == $_slug || 'completed' == $_slug ) ? '' : 'is_required_payment',
							'_report_order_status'  => ( 'processing' == $_slug || 'completed' == $_slug || 'on-hold' == $_slug ) ? 'on' : '',
						)
					)
				);
			}
		}

		\flush_rewrite_rules();
	}
}

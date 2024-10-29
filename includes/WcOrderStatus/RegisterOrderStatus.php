<?php

/**
 * it register all order statuses for WooCommerce Shop
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\WcOrderStatus
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\WcOrderStatus;

use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * AbstractRegisterOrderStatus
 *
 * The order status registration functionality
 */
class RegisterOrderStatus implements ServiceInterface {

    public function register(): void
    {
        \add_action( 'init', [$this, 'registerCustomStatuses'] );
        \add_filter( 'wc_order_statuses', [$this, 'mergeOrderStatuses'] );
    }

	/**
	 * Get custom order statuses
	 *
	 * @return array
	 */
	protected function getCustomOrderStatus() {
        $custom_statuses = get_posts(array(
            'post_type' 		=> 'wc_order_status',
            'post_status'       => 'publish',
            'posts_per_page'	=> -1,
        ));

        return $custom_statuses;
    }

    /**
	 * Register a status
	 *
	 * @return void
	 */
	public function registerCustomStatuses() {
		$custom_statuses = $this->getCustomOrderStatus();

		if( is_array( $custom_statuses ) ) {
			foreach( $custom_statuses as $custom_status ) {
				register_post_status($custom_status->post_name, array(
					'label'                     => $custom_status->post_title,
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( $custom_status->post_title . ' <span class="count">(%s)</span>', $custom_status->post_title . ' <span class="count">(%s)</span>' ) // phpcs:ignore
				));
			}
		}

	}

	/**
	 * Merge registered order status
	 *
	 * @param array $statuses
	 * @return array
	 */
	public function mergeOrderStatuses( $statuses ) {
		if( empty( $statuses ) ) return array();

		$custom_statuses = $this->getCustomOrderStatus();
		if( is_array( $custom_statuses ) ) {
			foreach( $custom_statuses as $custom_status ) {
				$statuses[$custom_status->post_name] = $custom_status->post_title;
			}
		}

		return $statuses;
	}

}

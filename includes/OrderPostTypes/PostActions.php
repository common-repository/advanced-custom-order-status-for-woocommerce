<?php

/**
 * File that holds base abstract class for custom post type actions.
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\OrderPostTypes
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\OrderPostTypes;

use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * AbstractPostActions
 *
 * Add action functionality for custom post type.
 */
class PostActions implements ServiceInterface {

    public function register(): void
    {
        \add_filter( 'post_row_actions', [$this, 'order_status_row_actions'], 100, 2 );
        \add_action( 'load-edit.php', [$this, 'order_status_permanent_delete'] );
        \add_filter( 'bulk_actions-edit-wc_order_status', '__return_empty_array' );
    }

    /**
     * Check whether the order status is default
     *
     * @return array
     */
    protected function isDefaultStatus( $post_id ) {
		$value = get_post_meta( $post_id, "_order_status_default", true );
    	return ( $value === '' ? true : false );
    }

	/**
	 * Check order status post type
	 *
	 * @param mixed $post_type
	 * @return bool
	 */
	protected function is_order_status_post_type( $post_type ) {
		return in_array( $post_type, array( 'wc_order_status', 'wc_order_email' ), true );
	}

    /**
	 * Order status row actions
	 *
	 * @param array $actions
	 * @param $post
	 * @return array
	 */
	public function order_status_row_actions( $actions, $post ) {

		if ( get_post_type() === 'wc_order_status' || get_post_type() === 'wc_order_email' ) {
			unset( $actions['inline hide-if-no-js'], $actions['trash'] );

			if ( current_user_can( 'delete_post', $post->ID ) && $this->isDefaultStatus( $post->ID ) ) {
				$actions['delete'] = sprintf(
					'<a class="submitdelete" title="%1$s" href="%2$s">%3$s</a>',
					esc_attr__( 'Delete', 'advanced-custom-order-status-for-woocommerce' ),
					get_delete_post_link( $post->ID, '', true ),
					__( 'Delete', 'advanced-custom-order-status-for-woocommerce' )
				);
			}
		}

		return $actions;
	}

    /**
	 * Force-delete any trashed order statuses or emails
	 *
	 * @since 1.0.0
	 */
	public function order_status_permanent_delete() {
		global $typenow;

		if ( $this->is_order_status_post_type( $typenow ) && isset( $_REQUEST['action'] ) && 'trash' === $_REQUEST['action'] ) {
			$_REQUEST['action'] = 'delete';
		}
	}

}

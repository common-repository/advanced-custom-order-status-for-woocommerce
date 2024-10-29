<?php

/**
 * Clean unnecessary custom post type filter and functionlities.
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\OrderPostTypes
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\OrderPostTypes;

use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * Post type cleaning from useless features for custom order status
 */
class CleanPostType implements ServiceInterface {

    public function register(): void
    {
        \add_action( 'restrict_manage_posts', [$this, 'removePostSearchFilter'] );
        \add_action( 'post_submitbox_misc_actions', [$this, 'removeUnnecessaryStatusInfo'] );
        \add_filter( 'months_dropdown_results', [$this, 'removeStatusDateFilter'], 10, 2 );
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
	 * Hide order status post filters and search bar.
	 *
	 * @return void
	 */
	public function removePostSearchFilter() {
		if( $this->is_order_status_screen() ) {
	    ?>
            <style type="text/css">
                div.wrap ul.subsubsub, div.wrap p.search-box, div.wrap .alignleft.actions {display: none;}
            </style>
	    <?php
		}
	}

    /**
	 * Remove submitbox statuses and infos
	 *
	 * @return void
	 */
	public function removeUnnecessaryStatusInfo() {
		if( $this->is_order_status_screen() ) {
		?>
			<style>
				div#post-body-content { margin-bottom: 0px; } #minor-publishing, #delete-action { display: none; } #wpbody { margin-top: 0 !important; }
			</style>
		<?php
		}
	}

    /**
	 * Remove Month filter from order status
	 *
	 * @param object $months
	 * @param string $post_type
	 * @return object
	 */
	public function removeStatusDateFilter( $months, $post_type ) {
		if( 'wc_order_status' == $post_type ) {
			return array();
		}

		return $months;
	}

}
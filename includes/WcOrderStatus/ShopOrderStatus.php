<?php

/**
 * Order Page targeted abstract class
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\WcOrderStatus
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\WcOrderStatus;

use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * AbstractShopOrderStatus
 *
 * AbstractShopOrderStatus holds custom order status functionality for order page.
 */
class ShopOrderStatus implements ServiceInterface {

    public function register(): void
    {
        \add_action( 'admin_head', [ $this, 'orderShopStatusColor' ] );
    }

    /**
     * Color order statuses in shop page
     *
     * @return void
     */
    public function orderShopStatusColor() {
        $_colors = \StorePlugin\CustomOrderStatus\Utils::get_order_attributes();

        if( is_array( $_colors ) ) {
            foreach( $_colors as $color ) {
                $_color     = get_post_meta( $color->ID, '_color_status', true );
                $_bgColor   = get_post_meta( $color->ID, '_bg_color_status', true );
                $_slug      = get_post_meta( $color->ID, "_slug_status", true );

                ?>
                <style>
                    .order-status.status-<?php echo esc_html( $_slug ); ?> {
                        color: <?php echo esc_html( $_color ); ?> !important;
                        background: <?php echo esc_html( $_bgColor ); ?> !important
                    }
                </style>
                <?php

            }
        }

    }

}

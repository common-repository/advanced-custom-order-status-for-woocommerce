<?php

/**
 * Holds metabox functionality to create it with array.
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\MetaBox
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\MetaBox;

use StorePlugin\CustomOrderStatus\Abstracts\AbstractMetaBoxes;
use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * OrderStatusMeta
 *
 * WooCommerce custom order status post type metabox
 */
class OrderStatusMeta extends AbstractMetaBoxes implements ServiceInterface {

    protected function ID(): string {
        return 'wc_order_status_meta';
    }

    protected function title(): string {
        return 'Order Statuses\' Fields';
    }

    protected function screen(): string {
        return 'wc_order_status';
    }

    private function dummyHTML() {
        ?>
            <table class="form-table wc-custom-order-status-form order-status-pro">
                <tbody>
                    <tr>
                        <th class="meta-titles"><label for="_icon_status"><?php _e( 'Select Icon:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                        <td>
                            <div class="wos__icons_field">
                                <fieldset class="wos__icons">
                                    <a>
                                        <span class="wos__icons--arrow dashicons dashicons-arrow-down"></span>
                                        <span class="wos__icons--selected"><?php _e( 'Select an icon', 'advanced-custom-order-status-for-woocommerce' ) ?></span>
                                    </a>
                                    <ul id="wos__select"></ul>
                                </fieldset>
                                <input type="submit" value="Reset" class="components-button is-primary" disabled>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="meta-titles"><label for="_action_list_order_status"><?php _e( 'Order statuses for actions:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                        <td>
                            <select name="_positioning_order_status" disabled>
                                <option><?php _e( 'Pending Payment', 'advanced-custom-order-status-for-woocommerce' ) ?></option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="meta-titles"><label for="_positioning_order_status"><?php _e( 'Position next to status:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                        <td>
                            <select name="_positioning_order_status" disabled>
                                <option><?php _e( 'Select from options', 'advanced-custom-order-status-for-woocommerce' ) ?></option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="meta-titles"><label for="_bulk_action_status"><?php _e( 'Bulk action:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                        <td><input type="checkbox" disabled></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="meta-titles"><label for="_payable_order_status"><?php _e( 'Requires Payment:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                        <td><input type="checkbox" disabled></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="meta-titles"><label for="_report_order_status"><?php _e( 'Show to report:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                        <td><input type="checkbox" disabled></td>
                        <td></td>
                    </tr>
                </tbody>
                <a href="https://storeplugin.net/plugins/advanced-custom-order-status-for-woocommerce/?utm_source=activesite&utm_campaign=corder&utm_medium=link" target="_blank"
                    style="text-decoration: none;border: 1px solid #666;font-size: 2rem;color: #FFF;border-radius: 8px;padding: 45px;padding-top: 20px;position: absolute;top: 70%;left: 65%;width: 323px;margin-left: -325px;height: 25px;margin-top: -13px;background: linear-gradient(90deg, rgba(85,85,85,1) 0%, rgba(83,103,107,1) 100%);z-index: 9;">
                    <?php _e( 'Check the pro version', 'advanced-custom-order-status-for-woocommerce' ); ?>
                </a>
            </table>
        <?php
    }

    /**
     * Order status input fields
     *
     * @param string $prefix
     * @return void
     */
    protected function addFields($prefix = '') {
        $fields = array(
            array(
                'name'          => __('Title:', 'advanced-custom-order-status-for-woocommerce'),
                'desc'          => __('Enter order status title', 'advanced-custom-order-status-for-woocommerce'),
                'id'            => "{$prefix}_title_status",
                'type'          => 'text',
                'placeholder'   => __('Enter order status title', 'advanced-custom-order-status-for-woocommerce'),
            ),
            array(
                'name'          => __('Slug:', 'advanced-custom-order-status-for-woocommerce'),
                'desc'          => __('Enter order status slug', 'advanced-custom-order-status-for-woocommerce'),
                'id'            => "{$prefix}_slug_status",
                'type'          => 'slug',
                'placeholder'   => __('Enter order status slug', 'advanced-custom-order-status-for-woocommerce'),
            ),
            array(
                'name'          => __('Description:', 'advanced-custom-order-status-for-woocommerce'),
                'desc'          => __('Enter order status descriptions', 'advanced-custom-order-status-for-woocommerce'),
                'id'            => "{$prefix}_desc_status",
                'type'          => 'textarea',
                'placeholder'   => __('Enter order status descriptions', 'advanced-custom-order-status-for-woocommerce'),
            ),
            array(
                'name'          => __('Color:', 'advanced-custom-order-status-for-woocommerce'),
                'desc'          => __('Enter status color', 'advanced-custom-order-status-for-woocommerce'),
                'id'            => "{$prefix}_color_status",
                'type'          => 'color',
                'default'       => '#777'
            ),
            array(
                'name'          => __('BG Color:', 'advanced-custom-order-status-for-woocommerce'),
                'desc'          => __('Enter status BG color', 'advanced-custom-order-status-for-woocommerce'),
                'id'            => "{$prefix}_bg_color_status",
                'type'          => 'color',
                'default'       => '#e5e5e5'
            ),
            array(
                'id'            => "{$prefix}_order_status_default",
                'type'          => 'hidden',
                'default'       => '',
            ),
        );

        $orderStatusFields = apply_filters( 'wc_order_status_fields', $fields, $prefix = '' );

        if( ! in_array( '_icon_status', array_column( $orderStatusFields, 'id' ) ) ) {
            $this->dummyHTML();
        }

        return $orderStatusFields;
    }
}

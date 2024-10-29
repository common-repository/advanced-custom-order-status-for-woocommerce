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
 * OrderEmailMeta
 *
 * WooCommerce custom order status post type metabox
 */
class OrderEmailMeta extends AbstractMetaBoxes implements ServiceInterface {

    protected function ID(): string {
        return 'wc_order_status_email_meta';
    }

    protected function title(): string {
        return 'Emails';
    }

    protected function screen(): string {
        return 'wc_order_email';
    }

    private function dummyHTML() {
    ?>
        <table class="form-table wc-custom-order-status-form order-status-pro">
            <tbody>
                <tr>
                    <th class="meta-titles"><label for="_status_email_to"><?php _e( 'Send email to:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                    <td>
                        <select disabled>
                            <option value="status_customer_email"><?php _e( 'Customer', 'advanced-custom-order-status-for-woocommerce' ) ?></option>
                        </select></td>
                    <td></td>
                </tr>
                <tr>
                    <th class="meta-titles"><label for="_status_email_title"><?php _e( 'Email title:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                    <td>
                        <input type="text" disabled placeholder="Enter email title">
                        <p class="desc"><?php _e( 'Email title', 'advanced-custom-order-status-for-woocommerce' ) ?></p>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th class="meta-titles"><label for="_status_email_subject"><?php _e( 'Email subject:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                    <td>
                        <input type="text" disabled placeholder="Enter email subject">
                        <p class="desc"><?php _e( 'Email subject', 'advanced-custom-order-status-for-woocommerce' ) ?></p>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th class="meta-titles"><label for="_status_email_body"><?php _e( 'Email Body:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                    <td>
                        <textarea placeholder="Write your email contents here" cols="60" rows="4" disabled></textarea>
                        <p class="desc">Write your email contents here</p>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th class="meta-titles"><label for="_order_status_from"><?php _e( 'Order status from:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                    <td>
                        <select disabled>
                            <option selected=""><?php _e( 'Select from options', 'advanced-custom-order-status-for-woocommerce' ) ?></option>
                        </select>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th class="meta-titles"><label for="_order_status_to"><?php _e( 'Order status to:', 'advanced-custom-order-status-for-woocommerce' ) ?></label></th>
                    <td>
                        <select disabled>
                            <option selected=""><?php _e( 'Select from options', 'advanced-custom-order-status-for-woocommerce' ) ?></option>
                        </select>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    <?php
    }

    /**
     * Add Order Email Pro Features
     *
     * @param string $prefix
     * @return array
     */
    protected function addFields($prefix = '') {
        $fields = apply_filters('wc_order_status_email', [], $prefix);
        if (!empty($fields)) {
            return $fields;
        }

        $this->dummyHTML();
    }
}

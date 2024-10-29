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
 * AbstractPostColumn
 *
 * Manage all necessary columns for custom order statuses.
 */
class PostColumn implements ServiceInterface {

    public function register(): void
    {
        \add_filter( 'manage_wc_order_status_posts_columns', [$this, 'addPostColumns'] );
        \add_action( 'manage_wc_order_status_posts_custom_column', [$this, 'setPostColumns'], 10, 2 );
        \add_filter( 'the_title', [$this, 'manage_order_status_title'], 10, 2);
    }

    /**
     * Add Custom Columns in Order Post Type
     *
     * @param array $columns
     * @return array
     */
    public function addPostColumns($columns) {
        $columns = array(
            'title'     => __('Title', 'advanced-custom-order-status-for-woocommerce'),
            'slug'      => __('Slug', 'advanced-custom-order-status-for-woocommerce'),
            'desc'      => __('Description', 'advanced-custom-order-status-for-woocommerce'),
            'icon'      => __('Icons', 'advanced-custom-order-status-for-woocommerce'),
        );

        return apply_filters('wc_order_status_column', $columns);
    }

    /**
     * Populate Order satus columns with the value
     *
     * @param string $column
     * @param int $post_id
     * @return void
     */
    public function setPostColumns($column, $post_id) {
        switch( $column ) {
            case 'title':
                echo esc_html( get_post_meta( $post_id, "_title_status", true ) );
                break;
            case 'slug':
                echo esc_html( get_post_meta( $post_id, "_slug_status", true ) );
                break;
            case 'desc':
                echo esc_html( get_post_meta( $post_id, "_desc_status", true ) );
                break;
            case 'used':
                do_action( 'wc_used_order_status_column', $post_id );
                break;
            case 'paid':
                do_action( 'wc_order_status_payable_column', $post_id );
                break;
            case 'report':
                do_action( 'wc_order_status_report_column', $post_id );
                break;
            case 'icon':
                echo wp_kses( $this->order_status_icon( $post_id ), 'post' );
                break;
        }
    }

    /**
     * Order status title
     *
     * @param int $post_id
     * @return string
     */
    protected function setOrderStatusTitle($post_id): string
    {
        return apply_filters( 'wc_order_status_title', get_post_meta($post_id, "_title_status", true ) );
    }

    /**
     * Order status slug
     *
     * @param int $post_id
     * @return string
     */
    protected function setOrderStatusSlug( $post_id ): string
    {
        return apply_filters( 'wc_order_status_slug', get_post_meta( $post_id, "_slug_status", true ) );
    }

    /**
     * Order status color
     *
     * @param int $post_id
     * @return string
     */
    protected function setOrderStatusColor($post_id): string
    {
        return apply_filters( 'wc_order_status_color', get_post_meta( $post_id, "_color_status", true ) );
    }

    /**
     * Order status post type
     *
     * @return string
     */
    protected function setOrderStatusPostType(): string
    {
        return 'wc_order_status';
    }

    /**
     * Set icon to order post column
     *
     * @param int $post_id
     * @return void
     */
    public function order_status_icon( $post_id ) {

        $_title     = $this->setOrderStatusTitle($post_id);
        $_slug      = get_post_meta( $post_id, "_slug_status", true );
        $dashicon   = apply_filters( 'wc_order_status_icon_class', $post_id );

        return '<span class="order-status status-icon status-'. $_slug .' '. $dashicon .'">'. (( gettype($dashicon) == 'integer' || empty( $dashicon ) ) ? $_title : '' ) .'</span>';

    }

    /**
     * Replace title value with title metabox value
     *
     * @param string $title
     * @param int $post_id
     * @return string
     */
    public function manage_order_status_title( $title, $post_id ) {
        if( is_admin() && $this->setOrderStatusPostType() == get_post_type($post_id)) {
            wp_update_post(array(
                'ID'            => $post_id,
                'post_status'   => 'publish',
                'post_title'    => $this->setOrderStatusTitle( $post_id ),
                'post_name'     => "wc-{$this->setOrderStatusSlug( $post_id )}",
            ));

            return $this->setOrderStatusTitle( $post_id );
        }

        return $title;

    }

}

<?php

/**
 * File that holds base abstract class for custom post type registration.
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\OrderPostTypes
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\OrderPostTypes;

use StorePlugin\CustomOrderStatus\Abstracts\AbstractPostType;
use StorePlugin\CustomOrderStatus\Contracts\ServiceInterface;

/**
 * File that holds base abstract class for custom post type registration.
 */
class PostOrderEmail extends AbstractPostType implements ServiceInterface {

	/**
	 * Post type slug constant.
	 *
	 * @var string
	 */
	public const POST_TYPE_SLUG = 'wc_order_email';

	/**
	 * URL slug for the custom post type.
	 *
	 * @var string
	 */
	public const POST_TYPE_URL_SLUG = '%rewrite_url%';

	/**
	 * Rest API Endpoint slug constant.
	 *
	 * @var string
	 */
	public const REST_API_ENDPOINT_SLUG = '%rest_endpoint_slug%';

	/**
	 * Capability type for projects post type.
	 *
	 * @var string
	 */
	public const POST_CAPABILITY_TYPE = 'post';

	/**
	 * Location of menu in sidebar.
	 *
	 * @var int
	 */
	public const MENU_POSITION = '%20%';

	/**
	 * Set menu icon.
	 *
	 * @var string
	 */
	public const MENU_ICON = '%menu_icon%';

	/**
	 * Get the slug to use for the Projects custom post type.
	 *
	 * @return string Custom post type slug.
	 */
	protected function getPostTypeSlug(): string
	{
		return self::POST_TYPE_SLUG;
	}

	/**
	 * Get the arguments that configure the Projects custom post type.
	 *
	 * @return array<mixed> Array of arguments.
	 */
	protected function getPostTypeArguments(): array
	{
		$nouns = [
			\esc_html_x(
				'Email',
				'post type upper case singular name',
				'advanced-custom-order-status-for-woocommerce'
			),
			\esc_html_x(
				'email',
				'post type lower case singular name',
				'advanced-custom-order-status-for-woocommerce'
			),
			\esc_html_x(
				'Emails',
				'post type upper case plural name',
				'advanced-custom-order-status-for-woocommerce'
			),
			\esc_html_x(
				'emails',
				'post type lower case plural name',
				'advanced-custom-order-status-for-woocommerce'
			),
		];

		$labels = $this->getGeneratedLabels($nouns);

		return [
			'label' => $nouns[0],
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'query_var' => false,
			'capability_type' => self::POST_CAPABILITY_TYPE,
			'has_archive' => true,
			'rewrite' => ['slug' => static::POST_TYPE_URL_SLUG],
			'hierarchical' => false,
			'menu_icon' => static::MENU_ICON,
			'menu_position' => static::MENU_POSITION,
			'supports' => ['page'],
			'show_in_rest' => false,
			'rest_base' => static::REST_API_ENDPOINT_SLUG,
		];
	}
}

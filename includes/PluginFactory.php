<?php

/**
 * The file that defines a factory for activating / deactivating plugin.
 *
 * @package StorePlugin\CustomOrderStatus
 */
namespace StorePlugin\CustomOrderStatus;

/**
 * The plugin factory class.
 */
class PluginFactory
{
	/**
	 * Activate the plugin.
	 */
	public static function activate(): void
	{
		(new Activate())->activate();
		Utils::deactivate_order_status_pro();
	}

	/**
	 * Deactivate the plugin.
	 */
	public static function deactivate(): void
	{
		(new Deactivate())->deactivate();
	}
}

<?php

namespace StorePlugin\CustomOrderStatus\Contracts;

interface ServiceInterface {
	/**
	 * Execute the class with singleton, factory method or WP hooks.
	 *
	 * @return void
	 */
	public function register(): void;
}

<?php

namespace StorePlugin\CustomOrderStatus;

use StorePlugin\CustomOrderStatus\Container\Dice;
use StorePlugin\CustomOrderStatus\Abstracts\Autowire;

/**
 * The file that defines the main start class.
 *
 * @package StorePlugin\CustomOrderStatus
 */
class Container extends Autowire
{
	/**
	 * Create DI container object with Dice
	 *
	 * @param Dice $container
	 * @return Autowire
	 */
    public function container( Dice $container ): Autowire
	{
		$this->container = $container;
		return $this;
	}

}

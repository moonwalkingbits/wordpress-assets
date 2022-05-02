<?php
/**
 * Assets: Asset registry interface
 *
 * @package Moonwalking_Bits\Assets
 * @author Martin Pettersson
 * @license GPL-2.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits\Assets;

use IteratorAggregate;

/**
 * Represents an asset registry.
 *
 * @since 0.1.0
 * @see \IteratorAggregate
 */
interface Asset_Registry_Interface extends IteratorAggregate {

	/**
	 * Registers a given asset.
	 *
	 * @since 0.1.0
	 * @param \Moonwalking_Bits\Assets\Abstract_Asset $asset Asset instance.
	 */
	public function register( Abstract_Asset $asset ): void;

	/**
	 * Registers an asset by the given parameters.
	 *
	 * This is a convenience method that assembles the needed pieces to create
	 * and register an asset.
	 *
	 * @since 0.1.0
	 * @param \Moonwalking_Bits\Assets\Asset_Type|string $type Asset type.
	 * @param string                                     $handle Asset unique identifier.
	 * @param string                                     $name Asset name.
	 * @return \Moonwalking_Bits\Assets\Abstract_Asset Registered asset instance.
	 */
	public function register_asset( $type, string $handle, string $name ): Abstract_Asset;

	/**
	 * Enqueues the registered assets.
	 */
	public function enqueue_assets(): void;
}

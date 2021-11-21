<?php
/**
 * Assets: Script asset class
 *
 * @package Moonwalking_Bits\Assets
 * @author Martin Pettersson
 * @license GPL-2.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits\Assets;

/**
 * Class representing a script asset.
 *
 * @since 0.1.0
 * @see \Moonwalking_Bits\Assets\Abstract_Asset
 */
class Script extends Abstract_Asset {

	/**
	 * Asset type.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public const TYPE = 'script';

	/**
	 * The location where this script should be loaded.
	 *
	 * @var \Moonwalking_Bits\Assets\Target_Location
	 */
	private Target_Location $target_location;

	/**
	 * Creates a new asset instance.
	 *
	 * @param string                                          $handle Unique identifier.
	 * @param string                                          $url Asset URL.
	 * @param string                                          $version Asset version.
	 * @param array                                           $dependencies List of asset dependencies.
	 * @param \Moonwalking_Bits\Assets\Target_Location|string $target_location The location where this script should be loaded.
	 */
	public function __construct(
		string $handle,
		string $url,
		string $version,
		array $dependencies = array(),
		$target_location = Target_Location::FOOTER
	) {
		parent::__construct( $handle, $url, $version, $dependencies );

		$this->target_location = $target_location instanceof Target_Location ?
			$target_location :
			Target_Location::from( $target_location );
	}

	/**
	 * Returns the location where this script should be loaded.
	 *
	 * @since 0.1.0
	 * @return \Moonwalking_Bits\Assets\Target_Location $target_location The location where this script should be loaded.
	 */
	public function target_location(): Target_Location {
		return $this->target_location;
	}
}

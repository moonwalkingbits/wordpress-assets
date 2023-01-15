<?php
/**
 * Assets: Script asset class
 *
 * @since 0.1.0
 * @author Martin Pettersson
 * @license GPL-2.0
 * @package Moonwalking_Bits\Assets
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
	 * Set of translations to register with the script.
	 *
	 * @var \Moonwalking_Bits\Assets\Translation[]
	 */
	private array $translations;

	/**
	 * Creates a new asset instance.
	 *
	 * @param string                                          $handle Unique identifier.
	 * @param string                                          $url Asset URL.
	 * @param string                                          $version Asset version.
	 * @param array                                           $dependencies List of asset dependencies.
	 * @param \Moonwalking_Bits\Assets\Target_Location|string $target_location The location where this script should be loaded.
	 * @param \Moonwalking_Bits\Assets\Translation[]          $translations Set of translations to register with the script.
	 */
	public function __construct(
		string $handle,
		string $url,
		string $version,
		array $dependencies = array(),
		$target_location = Target_Location::FOOTER,
		array $translations = array()
	) {
		parent::__construct( $handle, $url, $version, $dependencies );

		$this->target_location = $target_location instanceof Target_Location ?
			$target_location :
			Target_Location::from( $target_location );
		$this->translations    = $translations;
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

	/**
	 * Returns a set of translations to register with the script.
	 *
	 * @since 0.3.0
	 *
	 * @return \Moonwalking_Bits\Assets\Translation[] Registered translations.
	 */
	public function translations(): array {
		return $this->translations;
	}

	/**
	 * Adds a new translation to register with the script.
	 *
	 * @since 0.3.0
	 *
	 * @param \Moonwalking_Bits\Assets\Translation $translation Script translation instance.
	 *
	 * @return \Moonwalking_Bits\Assets\Script Same instance for method chaining.
	 */
	public function with( Translation $translation ): Script {
		$this->translations[] = $translation;

		return $this;
	}

	/**
	 * Adds a new translation to register with the script.
	 *
	 * @since 0.3.0
	 *
	 * @param string      $domain Text domain identifier.
	 * @param string|null $path Translation files path.
	 *
	 * @return \Moonwalking_Bits\Assets\Script Same instance for method chaining.
	 */
	public function with_translation( string $domain, ?string $path = null ): Script {
		return $this->with( new Translation( $domain, $path ) );
	}
}

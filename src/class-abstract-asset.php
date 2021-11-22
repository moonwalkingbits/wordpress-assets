<?php
/**
 * Assets: Abstract asset class
 *
 * @package Moonwalking_Bits\Assets
 * @author Martin Pettersson
 * @license GPL-2.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits\Assets;

/**
 * Class representing an abstract asset.
 *
 * This class is intended to be extended to create asset types. It provides
 * sensible default values for the most common parameters.
 *
 * @since 0.1.0
 */
abstract class Abstract_Asset {

	/**
	 * Asset type.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public const TYPE = 'abstract';

	/**
	 * Asset type unique identifier.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected string $handle;

	/**
	 * Asset URL.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected string $url;

	/**
	 * Asset version.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected string $version;

	/**
	 * List of asset dependencies.
	 *
	 * @since 0.1.0
	 * @var array
	 */
	protected array $dependencies;

	/**
	 * Whether the asset should be preloaded for optimization.
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	protected bool $should_be_preloaded;

	/**
	 * Creates a new asset instance.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 * @param string $handle Asset type unique identifier.
	 * @param string $url Asset URL.
	 * @param string $version Asset version.
	 * @param array  $dependencies List of asset dependencies.
	 * @param bool   $should_be_preloaded Whether the asset should be preloaded for optimization.
	 */
	public function __construct(
		string $handle,
		string $url,
		string $version,
		array $dependencies = array(),
		bool $should_be_preloaded = false
	) {
		$this->handle              = $handle;
		$this->url                 = $url;
		$this->version             = $version;
		$this->dependencies        = $dependencies;
		$this->should_be_preloaded = $should_be_preloaded;
	}

	/**
	 * Returns the asset type unique identifier.
	 *
	 * @since 0.1.0
	 * @return string Asset type unique identifier.
	 */
	public function handle(): string {
		return $this->handle;
	}

	/**
	 * Returns the asset URL.
	 *
	 * @since 0.1.0
	 * @return string Asset URL.
	 */
	public function url(): string {
		return $this->url;
	}

	/**
	 * Returns the asset version.
	 *
	 * @since 0.1.0
	 * @return string Asset version.
	 */
	public function version(): string {
		return $this->version;
	}

	/**
	 * Returns a list of asset dependencies.
	 *
	 * @since 0.1.0
	 * @return array A list of asset dependencies.
	 */
	public function dependencies(): array {
		return $this->dependencies;
	}

	/**
	 * Determines whether the asset should be preloaded for optimization.
	 *
	 * @since 0.1.0
	 * @return bool True if the asset should be preloaded for optimization.
	 */
	public function should_be_preloaded(): bool {
		return $this->should_be_preloaded;
	}

	/**
	 * Enable/disable asset preloading.
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 * @since 0.1.0
	 * @param bool $should_be_preloaded Wether to enable asset preloading.
	 */
	public function preload( bool $should_be_preloaded = true ): void {
		$this->should_be_preloaded = $should_be_preloaded;
	}
}

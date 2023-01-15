<?php
/**
 * Assets: Translation class
 *
 * @since 0.3.0
 * @author Martin Pettersson
 * @license GPL-2.0
 * @package Moonwalking_Bits\Assets
 */

namespace Moonwalking_Bits\Assets;

/**
 * Represents a script translation.
 *
 * @since 0.3.0
 */
class Translation {

	/**
	 * Text domain identifier.
	 *
	 * @var string
	 */
	private string $domain;

	/**
	 * Translation files path.
	 *
	 * @var string|null
	 */
	private ?string $path;

	/**
	 * Creates a new translation instance.
	 *
	 * @since 0.3.0
	 *
	 * @param string      $domain Text domain identifier.
	 * @param string|null $path Translation files path.
	 */
	public function __construct( string $domain, ?string $path ) {
		$this->domain = $domain;
		$this->path   = $path;
	}

	/**
	 * Returns the translation domain identifier.
	 *
	 * @since 0.3.0
	 *
	 * @return string Domain identifier.
	 */
	public function domain(): string {
		return $this->domain;
	}

	/**
	 * Returns the translation files path.
	 *
	 * @since 0.3.0
	 *
	 * @return string|null Translation files path.
	 */
	public function path(): ?string {
		return $this->path;
	}
}

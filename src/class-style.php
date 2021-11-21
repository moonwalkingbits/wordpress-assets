<?php
/**
 * Assets: Style asset class
 *
 * @package Moonwalking_Bits\Assets
 * @author Martin Pettersson
 * @license GPL-2.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits\Assets;

/**
 * Class representing a style asset.
 *
 * @since 0.1.0
 * @see \Moonwalking_Bits\Assets\Abstract_Asset
 */
class Style extends Abstract_Asset {

	/**
	 * Asset type.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public const TYPE = 'style';

	/**
	 * The media for which this stylesheet has been defined.
	 *
	 * @var \Moonwalking_Bits\Assets\Media_Type
	 */
	private Media_Type $media_type;

	/**
	 * Creates a new asset instance.
	 *
	 * @param string                                     $handle Unique identifier.
	 * @param string                                     $url Asset URL.
	 * @param string                                     $version Asset version.
	 * @param array                                      $dependencies List of asset dependencies.
	 * @param \Moonwalking_Bits\Assets\Media_Type|string $media_type The media for which this stylesheet has been defined.
	 */
	public function __construct(
		string $handle,
		string $url,
		string $version,
		array $dependencies = array(),
		$media_type = Media_Type::ALL
	) {
		parent::__construct( $handle, $url, $version, $dependencies );

		$this->media_type = $media_type instanceof Media_Type ?
			$media_type :
			Media_Type::from( $media_type );
	}

	/**
	 * Returns the media for which this stylesheet has been defined.
	 *
	 * @since 0.1.0
	 * @return \Moonwalking_Bits\Assets\Media_Type $media_type The media for which this stylesheet has been defined.
	 */
	public function media_type(): Media_Type {
		return $this->media_type;
	}
}

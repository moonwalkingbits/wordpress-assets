<?php
/**
 * Assets: Asset registry class
 *
 * @since 0.1.0
 * @author Martin Pettersson
 * @license GPL-2.0
 * @package Moonwalking_Bits\Assets
 */

namespace Moonwalking_Bits\Assets;

use ArrayIterator;
use Traversable;

/**
 * An asset registry class.
 *
 * @since 0.1.0
 * @see \Moonwalking_Bits\Assets\Asset_Registry_Interface
 */
class Asset_Registry implements Asset_Registry_Interface {

	/**
	 * Asset root directory.
	 *
	 * @var string
	 */
	private string $assets_directory;

	/**
	 * Asset root URL.
	 *
	 * @var string
	 */
	private string $assets_url;

	/**
	 * Registered assets.
	 *
	 * @var \Moonwalking_Bits\Assets\Abstract_Asset[]
	 */
	private array $assets;

	/**
	 * Creates a new asset registry instance.
	 *
	 * @since 0.1.0
	 *
	 * @param string $assets_directory Asset root directory.
	 * @param string $assets_url Asset root URL.
	 */
	public function __construct( string $assets_directory, string $assets_url ) {
		$this->assets_directory = rtrim( $assets_directory, '/' );
		$this->assets_url       = rtrim( $assets_url, '/' );
		$this->assets           = array();
	}

	/**
	 * Registers a given asset.
	 *
	 * @since 0.1.0
	 *
	 * @param \Moonwalking_Bits\Assets\Abstract_Asset $asset Asset instance.
	 */
	public function register( Abstract_Asset $asset ): void {
		$this->assets[] = $asset;
	}

	/**
	 * Registers an asset by the given parameters.
	 *
	 * This is a convenience method that assembles the needed pieces to create
	 * and register an asset.
	 *
	 * @since 0.1.0
	 *
	 * @param \Moonwalking_Bits\Assets\Asset_Type|string $type Asset type.
	 * @param string                                     $handle Asset unique identifier.
	 * @param string                                     $name Asset name.
	 *
	 * @return \Moonwalking_Bits\Assets\Abstract_Asset Registered asset instance.
	 */
	public function register_asset( $type, string $handle, string $name ): Abstract_Asset {
		if ( ! $type instanceof Asset_Type ) {
			$type = Asset_Type::from( $type );
		}

		$asset = $this->create_asset( $type, $handle, $name );

		$this->register( $asset );

		return $asset;
	}

	/**
	 * Enqueues the registered assets.
	 */
	public function enqueue_assets(): void {
		foreach ( $this->assets as $asset ) {
			$this->enqueue_asset( $asset );
		}
	}

	/**
	 * Create an asset instance of the given type.
	 *
	 * @param \Moonwalking_Bits\Assets\Asset_Type $type Asset type.
	 * @param string                              $handle Asset unique identifier.
	 * @param string                              $name Asset name.
	 *
	 * @return \Moonwalking_Bits\Assets\Abstract_Asset Asset instance.
	 */
	private function create_asset( Asset_Type $type, string $handle, string $name ): Abstract_Asset {
		if ( $type->value() === Asset_Type::STYLE ) {
			return $this->create_style_asset( $handle, $name );
		}

		return $this->create_script_asset( $handle, $name );
	}

	/**
	 * Creates a style asset based on the given parameters.
	 *
	 * @param string $handle Asset unique identifier.
	 * @param string $name Asset name.
	 *
	 * @return \Moonwalking_Bits\Assets\Style Style asset instance.
	 */
	private function create_style_asset( string $handle, string $name ): Style {
		list( 'version' => $version ) = require "{$this->assets_directory}/{$name}.asset.php";

		return new Style( $handle, "{$this->assets_url}/{$name}.css", $version, array() );
	}

	/**
	 * Creates a script asset based on the given parameters.
	 *
	 * @param string $handle Asset unique identifier.
	 * @param string $name Asset name.
	 *
	 * @return \Moonwalking_Bits\Assets\Script Script asset instance.
	 */
	private function create_script_asset( string $handle, string $name ): Script {
		list(
			'dependencies' => $dependencies,
			'version' => $version
			) = require "{$this->assets_directory}/{$name}.asset.php";

		return new Script( $handle, "{$this->assets_url}/{$name}.js", $version, $dependencies );
	}

	/**
	 * Enqueues a given asset.
	 *
	 * @param \Moonwalking_Bits\Assets\Abstract_Asset $asset Asset instance.
	 */
	private function enqueue_asset( Abstract_Asset $asset ): void {
		if ( $asset instanceof Style ) {
			wp_enqueue_style(
				$asset->handle(),
				$asset->url(),
				$asset->dependencies(),
				$asset->version(),
				$asset->media_type()->value()
			);
		}

		if ( $asset instanceof Script ) {
			wp_register_script(
				$asset->handle(),
				$asset->url(),
				$asset->dependencies(),
				$asset->version(),
				$asset->target_location()->value() === Target_Location::FOOTER
			);

			foreach ( $asset->translations() as $translation ) {
				wp_set_script_translations( $asset->handle(), $translation->domain(), (string) $translation->path() );
			}

			wp_enqueue_script( $asset->handle() );
		}

		if ( $asset->should_be_preloaded() ) {
			add_action( 'wp_head', fn() => $this->preload_asset( $asset ), 2 );
		}
	}

	/**
	 * Preloads the given asset.
	 *
	 * @param \Moonwalking_Bits\Assets\Abstract_Asset $asset Asset instance.
	 */
	private function preload_asset( Abstract_Asset $asset ): void {
		$type = $asset::TYPE;
		$href = apply_filters(
			"{$type}_loader_src",
			add_query_arg( 'ver', $asset->version(), $asset->url() ),
			$asset->handle()
		);

		if ( $asset instanceof Script ) {
			add_filter(
				'script_loader_tag',
				fn( string $tag, string $handle ) => $this->defer_script( $asset, $tag, $handle ),
				10,
				2
			);
		}

		printf( '<link rel="preload" href="%s" as="%s">%s', esc_attr( $href ), esc_attr( $type ), PHP_EOL );
	}

	/**
	 * Defers the given script.
	 *
	 * @param \Moonwalking_Bits\Assets\Script $script Script asset instance.
	 * @param string                          $tag Script HTML element.
	 * @param string                          $handle Unique identifier.
	 *
	 * @return string Script HTML element.
	 */
	private function defer_script( Script $script, string $tag, string $handle ): string {
		if ( $handle !== $script->handle() ) {
			return $tag;
		}

		return str_replace( '><', ' defer><', $tag );
	}

	/**
	 * Returns a traversable object of all registered assets.
	 *
	 * @return \Traversable Traversable object of all registered assets.
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator( $this->assets );
	}
}

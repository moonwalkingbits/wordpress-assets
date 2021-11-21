<?php

namespace Moonwalking_Bits\Assets;

use Moonwalking_Bits\Templating\Template_Engine_Interface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use phpmock\phpunit\PHPMock;

class Asset_Registry_Test extends TestCase {

	use PHPMock;

	private string $assets_directory;
	private string $assets_url;
	private MockObject $template_engine_mock;
	private Asset_Registry $assets;
	private MockObject $add_action_mock;
	private MockObject $add_filter_mock;
	private MockObject $apply_filters_mock;
	private MockObject $add_query_arg_mock;
	private MockObject $wp_enqueue_style_mock;
	private MockObject $wp_enqueue_script_mock;

	/**
	 * @before
	 */
	public function set_up(): void {
		$this->assets_directory       = __DIR__ . '/fixtures/assets/';
		$this->assets_url             = 'http://example.com/assets';
		$this->template_engine_mock   = $this->getMockBuilder( Template_Engine_Interface::class )->getMock();
		$this->assets                 = new Asset_Registry(
			$this->assets_directory,
			$this->assets_url,
			$this->template_engine_mock
		);
		$this->add_action_mock        = $this->getFunctionMock( __NAMESPACE__, 'add_action' );
		$this->add_filter_mock        = $this->getFunctionMock( __NAMESPACE__, 'add_filter' );
		$this->apply_filters_mock     = $this->getFunctionMock( __NAMESPACE__, 'apply_filters' );
		$this->add_query_arg_mock     = $this->getFunctionMock( __NAMESPACE__, 'add_query_arg' );
		$this->wp_enqueue_style_mock  = $this->getFunctionMock( __NAMESPACE__, 'wp_enqueue_style' );
		$this->wp_enqueue_script_mock = $this->getFunctionMock( __NAMESPACE__, 'wp_enqueue_script' );
	}

	/**
	 * @test
	 */
	public function should_enqueue_style_asset_when_registered(): void {
		$handle       = 'handle';
		$url          = 'url';
		$dependencies = array( 'one' );
		$version      = 'version';
		$media_type   = Media_Type::from( 'all' );
		$asset_mock   = $this->getMockBuilder( Style::class )
			->disableOriginalConstructor()
			->getMock();

		$asset_mock->method( 'handle' )->will( $this->returnValue( $handle ) );
		$asset_mock->method( 'url' )->will( $this->returnValue( $url ) );
		$asset_mock->method( 'dependencies' )->will( $this->returnValue( $dependencies ) );
		$asset_mock->method( 'version' )->will( $this->returnValue( $version ) );
		$asset_mock->method( 'media_type' )->will( $this->returnValue( $media_type ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( false ) );

		$this->add_action_mock->expects( $this->once() )
			->with( 'wp_enqueue_scripts', $this->isType( 'callable' ) )
			->will( $this->returnCallback( fn( string $action, callable $callable ) => $callable() ) );
		$this->wp_enqueue_style_mock->expects( $this->once() )
			->with( $handle, $url, $dependencies, $version, $media_type->value() );

		$this->assets->register( $asset_mock );
	}

	/**
	 * @test
	 */
	public function should_preload_style_asset(): void {
		$handle       = 'handle';
		$url          = 'url';
		$dependencies = array( 'one' );
		$version      = 'version';
		$media_type   = Media_Type::from( 'all' );
		$asset_mock   = $this->getMockBuilder( Style::class )
			->disableOriginalConstructor()
			->getMock();

		$asset_mock->method( 'handle' )->will( $this->returnValue( $handle ) );
		$asset_mock->method( 'url' )->will( $this->returnValue( $url ) );
		$asset_mock->method( 'dependencies' )->will( $this->returnValue( $dependencies ) );
		$asset_mock->method( 'version' )->will( $this->returnValue( $version ) );
		$asset_mock->method( 'media_type' )->will( $this->returnValue( $media_type ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( true ) );

		$this->add_action_mock->expects( $this->exactly( 2 ) )
			->withConsecutive(
				array(
					'wp_enqueue_scripts',
					$this->isType( 'callable' ),
				),
				array(
					'wp_head',
					$this->isType( 'callable' ),
				),
			)
			->will( $this->returnCallback( fn( string $action, callable $callable ) => $callable() ) );
		$this->wp_enqueue_style_mock->expects( $this->once() )
			->with( $handle, $url, $dependencies, $version, $media_type->value() );
		$this->template_engine_mock->expects( $this->once() )
			->method( 'render' )
			->with( 'assets/preload-link' );
		$this->apply_filters_mock->expects( $this->any() )->will( $this->returnValue( $url ) );
		$this->add_query_arg_mock->expects( $this->any() )->will( $this->returnValue( $url ) );

		$this->assets->register( $asset_mock );
	}

	/**
	 * @test
	 */
	public function should_enqueue_script_asset_when_registered(): void {
		$handle          = 'handle';
		$url             = 'url';
		$dependencies    = array( 'one' );
		$version         = 'version';
		$target_location = Target_Location::from( 'FOOTER' );
		$asset_mock      = $this->getMockBuilder( Script::class )
			->disableOriginalConstructor()
			->getMock();

		$asset_mock->method( 'handle' )->will( $this->returnValue( $handle ) );
		$asset_mock->method( 'url' )->will( $this->returnValue( $url ) );
		$asset_mock->method( 'dependencies' )->will( $this->returnValue( $dependencies ) );
		$asset_mock->method( 'version' )->will( $this->returnValue( $version ) );
		$asset_mock->method( 'target_location' )->will( $this->returnValue( $target_location ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( false ) );

		$this->add_action_mock->expects( $this->once() )
			->with( 'wp_enqueue_scripts', $this->isType( 'callable' ) )
			->will( $this->returnCallback( fn( string $action, callable $callable ) => $callable() ) );
		$this->wp_enqueue_script_mock->expects( $this->once() )
			->with( $handle, $url, $dependencies, $version, true );

		$this->assets->register( $asset_mock );
	}

	/**
	 * @test
	 */
	public function should_preload_script_asset(): void {
		$handle            = 'handle';
		$url               = 'url';
		$dependencies      = array( 'one' );
		$version           = 'version';
		$target_location   = Target_Location::from( 'FOOTER' );
		$asset_mock        = $this->getMockBuilder( Script::class )
			->disableOriginalConstructor()
			->getMock();

		$asset_mock->method( 'handle' )->will( $this->returnValue( $handle ) );
		$asset_mock->method( 'url' )->will( $this->returnValue( $url ) );
		$asset_mock->method( 'dependencies' )->will( $this->returnValue( $dependencies ) );
		$asset_mock->method( 'version' )->will( $this->returnValue( $version ) );
		$asset_mock->method( 'target_location' )->will( $this->returnValue( $target_location ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( true ) );

		$this->add_action_mock->expects( $this->exactly( 2 ) )
			->withConsecutive(
				array(
					'wp_enqueue_scripts',
					$this->isType( 'callable' ),
				),
				array(
					'wp_head',
					$this->isType( 'callable' ),
				),
			)
			->will( $this->returnCallback( fn( string $hook, callable $callable ) => $callable() ) );
		$this->add_filter_mock->expects( $this->once() )
			->with(
				'script_loader_tag',
				$this->callback(
					function ( callable $filter ) use ( $handle ) {
						return (
							$filter( '<script></script>', $handle ) === '<script defer></script>' &&
							$filter( '<script></script>', 'other-handle' ) === '<script></script>'
						);
					}
				)
			);
		$this->wp_enqueue_script_mock->expects( $this->once() )
			->with( $handle, $url, $dependencies, $version, true );
		$this->template_engine_mock->expects( $this->once() )
			->method( 'render' )
			->with( 'assets/preload-link' );
		$this->apply_filters_mock->expects( $this->any() )->will( $this->returnValue( $url ) );
		$this->add_query_arg_mock->expects( $this->any() )->will( $this->returnValue( $url ) );

		$this->assets->register( $asset_mock );
	}

	/**
	 * @test
	 */
	public function should_register_style_from_asset_file(): void {
		$this->add_action_mock->expects( $this->once() )
			->with( 'wp_enqueue_scripts', $this->isType( 'callable' ) )
			->will( $this->returnCallback( fn( string $action, callable $callable ) => $callable() ) );
		$this->wp_enqueue_style_mock->expects( $this->once() )
			->with( 'style', "{$this->assets_url}/style.scss.css", array(), 'version', 'all' );

		$this->assets->register_asset( Asset_Type::STYLE, 'style', 'style.scss' );
	}

	/**
	 * @test
	 */
	public function should_register_script_from_asset_file(): void {
		$this->add_action_mock->expects( $this->once() )
			->with( 'wp_enqueue_scripts', $this->isType( 'callable' ) )
			->will( $this->returnCallback( fn( string $action, callable $callable ) => $callable() ) );
		$this->wp_enqueue_script_mock->expects( $this->once() )
			->with( 'script', "{$this->assets_url}/script.js", array( 'one' ), 'version', true );

		$this->assets->register_asset( Asset_Type::SCRIPT, 'script', 'script' );
	}
}

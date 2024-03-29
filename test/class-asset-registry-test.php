<?php

namespace Moonwalking_Bits\Assets;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class Asset_Registry_Test extends TestCase {

	use PHPMock;

	private string $assets_directory;
	private string $assets_url;
	private Asset_Registry $assets;
	private MockObject $add_action_mock;
	private MockObject $add_filter_mock;
	private MockObject $apply_filters_mock;
	private MockObject $add_query_arg_mock;
	private MockObject $wp_enqueue_style_mock;
	private MockObject $wp_register_script_mock;
	private MockObject $wp_set_script_translations_mock;
	private MockObject $wp_enqueue_script_mock;
	private MockObject $esc_attr_mock;

	/**
	 * @before
	 */
	public function set_up(): void {
		$this->assets_directory                = __DIR__ . '/fixtures/assets/';
		$this->assets_url                      = 'http://example.com/assets';
		$this->assets                          = new Asset_Registry( $this->assets_directory, $this->assets_url );
		$this->add_action_mock                 = $this->getFunctionMock( __NAMESPACE__, 'add_action' );
		$this->add_filter_mock                 = $this->getFunctionMock( __NAMESPACE__, 'add_filter' );
		$this->apply_filters_mock              = $this->getFunctionMock( __NAMESPACE__, 'apply_filters' );
		$this->add_query_arg_mock              = $this->getFunctionMock( __NAMESPACE__, 'add_query_arg' );
		$this->wp_enqueue_style_mock           = $this->getFunctionMock( __NAMESPACE__, 'wp_enqueue_style' );
		$this->wp_register_script_mock         = $this->getFunctionMock( __NAMESPACE__, 'wp_register_script' );
		$this->wp_set_script_translations_mock = $this->getFunctionMock( __NAMESPACE__, 'wp_set_script_translations' );
		$this->wp_enqueue_script_mock          = $this->getFunctionMock( __NAMESPACE__, 'wp_enqueue_script' );
		$this->esc_attr_mock                   = $this->getFunctionMock( __NAMESPACE__, 'esc_attr' );
	}

	/**
	 * @test
	 */
	public function should_enqueue_registered_style_assets(): void {
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

		$this->wp_enqueue_style_mock->expects( $this->once() )
		                            ->with( $handle, $url, $dependencies, $version, $media_type->value() );

		$this->assets->register( $asset_mock );
		$this->assets->enqueue_assets();
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

		$this->expectOutputString( '<link rel="preload" href="' . $url . '" as="style">' . PHP_EOL );

		$asset_mock->method( 'handle' )->will( $this->returnValue( $handle ) );
		$asset_mock->method( 'url' )->will( $this->returnValue( $url ) );
		$asset_mock->method( 'dependencies' )->will( $this->returnValue( $dependencies ) );
		$asset_mock->method( 'version' )->will( $this->returnValue( $version ) );
		$asset_mock->method( 'media_type' )->will( $this->returnValue( $media_type ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( true ) );

		$this->add_action_mock->expects( $this->once() )
		                      ->with( 'wp_head', $this->isType( 'callable' ) )
		                      ->will( $this->returnCallback( fn( string $action, callable $callable ) => $callable() ) );
		$this->wp_enqueue_style_mock->expects( $this->once() )
		                            ->with( $handle, $url, $dependencies, $version, $media_type->value() );
		$this->apply_filters_mock->expects( $this->any() )->will( $this->returnValue( $url ) );
		$this->add_query_arg_mock->expects( $this->any() )->will( $this->returnValue( $url ) );
		$this->esc_attr_mock->expects( $this->any() )->will( $this->returnCallback( fn( $value ) => $value ) );

		$this->assets->register( $asset_mock );
		$this->assets->enqueue_assets();
	}

	/**
	 * @test
	 */
	public function should_enqueue_registered_script_assets(): void {
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
		$asset_mock->method( 'translations' )->will( $this->returnValue( array() ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( false ) );

		$this->wp_register_script_mock->expects( $this->once() )->with( $handle, $url, $dependencies, $version, true );
		$this->wp_enqueue_script_mock->expects( $this->once() )->with( $handle );

		$this->assets->register( $asset_mock );
		$this->assets->enqueue_assets();
	}

	/**
	 * @test
	 */
	public function should_set_script_translations(): void {
		$handle             = 'handle';
		$translation_domain = 'domain';
		$translation_path   = 'path';
		$asset_mock         = $this->getMockBuilder( Script::class )
		                           ->disableOriginalConstructor()
		                           ->getMock();
		$translation_mock   = $this->getMockBuilder( Translation::class )
		                           ->disableOriginalConstructor()
		                           ->getMock();

		$asset_mock->method( 'handle' )->will( $this->returnValue( $handle ) );
		$asset_mock->method( 'url' )->will( $this->returnValue( '' ) );
		$asset_mock->method( 'dependencies' )->will( $this->returnValue( array() ) );
		$asset_mock->method( 'version' )->will( $this->returnValue( '' ) );
		$asset_mock->method( 'target_location' )->will( $this->returnValue( Target_Location::from( 'FOOTER' ) ) );
		$asset_mock->method( 'translations' )->will( $this->returnValue( array( $translation_mock ) ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( false ) );

		$translation_mock->method( 'domain' )->will( $this->returnValue( $translation_domain ) );
		$translation_mock->method( 'path' )->will( $this->returnValue( $translation_path ) );

		$this->wp_enqueue_script_mock->expects( $this->once() )->with( $handle );
		$this->wp_set_script_translations_mock
			->expects( $this->once() )
			->with( $handle, $translation_domain, $translation_path );

		$this->assets->register( $asset_mock );
		$this->assets->enqueue_assets();
	}

	/**
	 * @test
	 */
	public function should_preload_script_asset(): void {
		$handle          = 'handle';
		$url             = 'url';
		$dependencies    = array( 'one' );
		$version         = 'version';
		$target_location = Target_Location::from( 'FOOTER' );
		$asset_mock      = $this->getMockBuilder( Script::class )
		                        ->disableOriginalConstructor()
		                        ->getMock();

		$this->expectOutputString( '<link rel="preload" href="' . $url . '" as="script">' . PHP_EOL );

		$asset_mock->method( 'handle' )->will( $this->returnValue( $handle ) );
		$asset_mock->method( 'url' )->will( $this->returnValue( $url ) );
		$asset_mock->method( 'dependencies' )->will( $this->returnValue( $dependencies ) );
		$asset_mock->method( 'version' )->will( $this->returnValue( $version ) );
		$asset_mock->method( 'target_location' )->will( $this->returnValue( $target_location ) );
		$asset_mock->method( 'should_be_preloaded' )->will( $this->returnValue( true ) );

		$this->add_action_mock->expects( $this->once() )
		                      ->with( 'wp_head', $this->isType( 'callable' ) )
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
		$this->wp_register_script_mock->expects( $this->once() )->with( $handle, $url, $dependencies, $version, true );
		$this->wp_enqueue_script_mock->expects( $this->once() )->with( $handle );
		$this->apply_filters_mock->expects( $this->any() )->will( $this->returnValue( $url ) );
		$this->add_query_arg_mock->expects( $this->any() )->will( $this->returnValue( $url ) );
		$this->esc_attr_mock->expects( $this->any() )->will( $this->returnCallback( fn( $value ) => $value ) );

		$this->assets->register( $asset_mock );
		$this->assets->enqueue_assets();
	}

	/**
	 * @test
	 */
	public function should_register_style_from_asset_file(): void {
		$this->wp_enqueue_style_mock->expects( $this->once() )
		                            ->with( 'style', "{$this->assets_url}/style.css", array(), 'version', 'all' );

		$this->assets->register_asset( Asset_Type::STYLE, 'style', 'style' );
		$this->assets->enqueue_assets();
	}

	/**
	 * @test
	 */
	public function should_register_script_from_asset_file(): void {
		$this->wp_register_script_mock->expects( $this->once() )
		                              ->with( 'script', "{$this->assets_url}/script.js", array( 'one' ), 'version', true );
		$this->wp_enqueue_script_mock->expects( $this->once() )->with( 'script' );

		$this->assets->register_asset( Asset_Type::SCRIPT, 'script', 'script' );
		$this->assets->enqueue_assets();
	}

	/**
	 * @test
	 */
	public function should_be_iterable(): void {
		$asset_mock = $this->getMockBuilder( Script::class )
		                   ->disableOriginalConstructor()
		                   ->getMock();

		$this->assets->register( $asset_mock );

		$this->assertEquals( 1, count( $this->assets->getIterator() ) );
		$this->assertSame( $asset_mock, $this->assets->getIterator()[0] );
	}

	/**
	 * @test
	 */
	public function should_remove_registered_asset(): void {
		$script_mock = $this->getMockBuilder( Script::class )
		                    ->disableOriginalConstructor()
		                    ->getMock();
		$style_mock  = $this->getMockBuilder( Style::class )
		                    ->disableOriginalConstructor()
		                    ->getMock();

		$this->assets->register( $script_mock );
		$this->assets->register( $style_mock );

		$this->assets->deregister( $script_mock );

		$this->assertEquals( 1, count( $this->assets->getIterator() ) );
		$this->assertSame( $style_mock, $this->assets->getIterator()[0] );

		$this->assets->deregister( $style_mock );

		$this->assertEmpty( $this->assets->getIterator() );
	}

	/**
	 * @test
	 */
	public function should_remove_registered_asset_by_parameters(): void {
		$script_mock = $this->getMockBuilder( Script::class )
		                    ->disableOriginalConstructor()
		                    ->getMock();
		$style_mock  = $this->getMockBuilder( Style::class )
		                    ->disableOriginalConstructor()
		                    ->getMock();

		$script_mock->method( 'handle' )->will( $this->returnValue( 'script' ) );
		$style_mock->method( 'handle' )->will( $this->returnValue( 'style' ) );

		$this->assets->register( $script_mock );
		$this->assets->register( $style_mock );

		$this->assets->deregister_asset( Asset_Type::SCRIPT, 'script' );

		$this->assertEquals( 1, count( $this->assets->getIterator() ) );
		$this->assertSame( $style_mock, $this->assets->getIterator()[0] );

		$this->assets->deregister_asset( Asset_Type::STYLE, 'style' );

		$this->assertEmpty( $this->assets->getIterator() );
	}

	/**
	 * @test
	 */
	public function should_not_throw_exception_if_asset_is_not_found_when_deregistering(): void {
		$this->assets->deregister(
			$this->getMockBuilder( Script::class )
			     ->disableOriginalConstructor()
			     ->getMock()
		);
	}
}

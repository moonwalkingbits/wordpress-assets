<?php

namespace Moonwalking_Bits\Assets;

use PHPUnit\Framework\TestCase;

class Script_Test extends TestCase {
	private Script $script;

	/**
	 * @before
	 */
	public function set_up(): void {
		$this->script = new Script( 'handle', 'url', 'version' );
	}

	/**
	 * @test
	 */
	public function should_initialize_all_properties(): void {
		$handle           = 'handle';
		$url              = 'url';
		$version          = 'version';
		$dependency       = 'one';
		$target_location  = Target_Location::from( 'FOOTER' );
		$translation_mock = $this->getMockBuilder( Translation::class )
		                         ->disableOriginalConstructor()
		                         ->getMock();
		$script           = new Script(
			$handle,
			$url,
			$version,
			array( $dependency ),
			$target_location,
			array( $translation_mock )
		);

		$this->assertEquals( $handle, $script->handle() );
		$this->assertEquals( $url, $script->url() );
		$this->assertEquals( $version, $script->version() );
		$this->assertEquals( array( $dependency ), $script->dependencies() );
		$this->assertEquals( $target_location, $script->target_location() );
		$this->assertEquals( array( $translation_mock ), $script->translations() );
	}

	/**
	 * @test
	 */
	public function should_add_translation_instance(): void {
		$translation_mock = $this->getMockBuilder( Translation::class )
		                         ->disableOriginalConstructor()
		                         ->getMock();

		$this->script->with( $translation_mock );

		$this->assertEquals( array( $translation_mock ), $this->script->translations() );
	}

	/**
	 * @test
	 */
	public function should_add_translation(): void {
		$this->script->with_translation( 'domain', 'path' );

		$this->assertCount( 1, $this->script->translations() );
		$this->assertContainsOnly( Translation::class, $this->script->translations() );
	}

	/**
	 * @test
	 */
	public function with_should_return_same_instance(): void {
		$script = $this->script->with(
			$this->getMockBuilder( Translation::class )
			     ->disableOriginalConstructor()
			     ->getMock()
		);

		$this->assertSame( $this->script, $script );
	}

	/**
	 * @test
	 */
	public function with_translation_should_return_same_instance(): void {
		$script = $this->script->with_translation( 'domain' );

		$this->assertSame( $this->script, $script );
	}
}

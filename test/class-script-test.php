<?php

namespace Moonwalking_Bits\Assets;

use PHPUnit\Framework\TestCase;

class Script_Test extends TestCase {
	/**
	 * @test
	 */
	public function should_initialize_all_properties(): void {
		$handle = 'handle';
		$url = 'url';
		$version = 'version';
		$dependencies = array( 'one' );
		$target_location = Target_Location::from( 'FOOTER' );
		$style = new Script( $handle, $url, $version, $dependencies, $target_location );

		$this->assertEquals( $handle, $style->handle() );
		$this->assertEquals( $url, $style->url() );
		$this->assertEquals( $version, $style->version() );
		$this->assertEquals( $dependencies, $style->dependencies() );
		$this->assertEquals( $target_location, $style->target_location() );
	}
}

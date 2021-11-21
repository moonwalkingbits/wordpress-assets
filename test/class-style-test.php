<?php

namespace Moonwalking_Bits\Assets;

use PHPUnit\Framework\TestCase;

class Style_Test extends TestCase {
	/**
	 * @test
	 */
	public function should_initialize_all_properties(): void {
		$handle = 'handle';
		$url = 'url';
		$version = 'version';
		$dependencies = array( 'one' );
		$media_type = Media_Type::from( 'all' );
		$style = new Style( $handle, $url, $version, $dependencies, $media_type );

		$this->assertEquals( $handle, $style->handle() );
		$this->assertEquals( $url, $style->url() );
		$this->assertEquals( $version, $style->version() );
		$this->assertEquals( $dependencies, $style->dependencies() );
		$this->assertEquals( $media_type, $style->media_type() );
	}
}

<?php

namespace Moonwalking_Bits\Assets;

use PHPUnit\Framework\TestCase;

class Translation_Test extends TestCase {
	/**
	 * @test
	 */
	public function should_initialize_all_properties(): void {
		$domain      = 'domain';
		$path        = 'path';
		$translation = new Translation( $domain, $path );

		$this->assertEquals( $domain, $translation->domain() );
		$this->assertEquals( $path, $translation->path() );
	}
}

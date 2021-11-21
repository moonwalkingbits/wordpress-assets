<?php

namespace Moonwalking_Bits\Assets;

use PHPUnit\Framework\TestCase;

class Abstract_Asset_Test extends TestCase {
	/**
	 * @test
	 */
	public function should_enable_preloading(): void {
		$asset_mock = $this->getMockBuilder( Abstract_Asset::class )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$asset_mock->preload();

		$this->assertTrue( $asset_mock->should_be_preloaded() );
	}
}

<?php
use Inc2734\WP_View_Controller\Helper;

class Inc2734_WP_View_Controller_Config_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();
		new \Inc2734\WP_View_Controller\Bootstrap();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function get() {
		$config = \Inc2734\WP_View_Controller\App\Config::get( 'no-match' );
		$this->assertNull( $config );

		$config = \Inc2734\WP_View_Controller\App\Config::get();
		$this->assertTrue( is_array( $config ) );

		$config = \Inc2734\WP_View_Controller\App\Config::get( 'layout' );
		$this->assertEquals( [ 'templates/layout/wrapper' ], $config );

		$config = Helper\config( 'no-match' );
		$this->assertNull( $config );

		$config = Helper\config();
		$this->assertTrue( is_array( $config ) );

		$config = Helper\config( 'layout' );
		$this->assertEquals( [ 'templates/layout/wrapper' ], $config );
	}
}

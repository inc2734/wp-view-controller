<?php
class Inc2734_WP_View_Controller_Config_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();
		new \Inc2734\WP_View_Controller\View_Controller();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function get() {
		$config = \Inc2734\WP_View_Controller\App\Config_Loader::get( 'no-match' );
		$this->assertNull( $config );

		$config = \Inc2734\WP_View_Controller\App\Config_Loader::get();
		$this->assertTrue( is_array( $config ) );

		$config = \Inc2734\WP_View_Controller\App\Config_Loader::get( 'layout' );
		$this->assertEquals( [ 'templates/layout/wrapper' ], $config );

		$config = wpvc_config( 'no-match' );
		$this->assertNull( $config );

		$config = wpvc_config();
		$this->assertTrue( is_array( $config ) );

		$config = wpvc_config( 'layout' );
		$this->assertEquals( [ 'templates/layout/wrapper' ], $config );
	}
}

<?php
class Inc2734_WP_View_Controller_Config_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();
		include_once( __DIR__ . '/../src/wp-view-controller.php' );
		new Inc2734_WP_View_Controller();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function get() {
		$config = Inc2734_WP_View_Controller_Config::get( 'no-match' );
		$this->assertNull( $config );

		$config = Inc2734_WP_View_Controller_Config::get();
		$this->assertTrue( is_array( $config ) );

		$config = Inc2734_WP_View_Controller_Config::get( 'layout' );
		$this->assertEquals( 'templates/layout/wrapper', $config );

		$config = wpvc_config( 'no-match' );
		$this->assertNull( $config );

		$config = wpvc_config();
		$this->assertTrue( is_array( $config ) );

		$config = wpvc_config( 'layout' );
		$this->assertEquals( 'templates/layout/wrapper', $config );
	}
}

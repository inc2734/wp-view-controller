<?php
use Inc2734\WP_View_Controller\App\Config;
use Inc2734\WP_View_Controller\Helper;

class Inc2734_WP_View_Controller_Config_Test extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		new \Inc2734\WP_View_Controller\Bootstrap();
	}

	public function tear_down() {
		parent::tear_down();
	}

	/**
	 * @test
	 */
	public function get() {
		$config = Config::get( 'no-match' );
		$this->assertNull( $config );

		$config = Config::get();
		$this->assertTrue( is_array( $config ) );

		$config = Config::get( 'layout' );
		$this->assertEquals( [ 'templates/layout/wrapper' ], $config );

		$config = Config::get( 'no-match' );
		$this->assertNull( $config );

		$config = Config::get();
		$this->assertTrue( is_array( $config ) );

		$config = Config::get( 'layout' );
		$this->assertEquals( [ 'templates/layout/wrapper' ], $config );
	}
}

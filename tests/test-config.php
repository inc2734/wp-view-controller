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

	/**
	 * @test
	 */
	public function get__default_config_filter_is_applied_on_every_call() {
		add_filter(
			'inc2734_wp_view_controller_config',
			function( $config ) {
				$config['layout'] = [ get_option( 'wp_view_controller_layout', 'templates/layout/wrapper' ) ];
				return $config;
			}
		);

		update_option( 'wp_view_controller_layout', 'templates/layout/custom-wrapper' );
		$this->assertEquals( [ 'templates/layout/custom-wrapper' ], Config::get( 'layout' ) );

		update_option( 'wp_view_controller_layout', 'templates/layout/updated-wrapper' );
		$this->assertEquals( [ 'templates/layout/updated-wrapper' ], Config::get( 'layout' ) );
	}

	/**
	 * @test
	 */
	public function get__custom_config_path_is_loaded_on_every_call() {
		$config_path = untrailingslashit( sys_get_temp_dir() ) . '/wp-view-controller-config.php';
		file_exists( $config_path ) && unlink( $config_path );

		add_filter(
			'inc2734_wp_view_controller_config_path',
			function() use ( $config_path ) {
				return $config_path;
			}
		);

		add_filter(
			'inc2734_wp_view_controller_config',
			function( $config ) {
				$config['footer'] = [ get_option( 'wp_view_controller_footer', 'templates/layout/footer' ) ];
				return $config;
			}
		);

		update_option( 'wp_view_controller_footer', 'templates/layout/custom-footer' );
		file_put_contents( $config_path, "<?php return array( 'layout' => array( 'templates/layout/custom-wrapper' ) );" );
		$config = Config::get();
		$this->assertEquals( [ 'templates/layout/custom-wrapper' ], $config['layout'] );
		$this->assertEquals( [ 'templates/layout/custom-footer' ], $config['footer'] );

		update_option( 'wp_view_controller_footer', 'templates/layout/updated-footer' );
		file_put_contents( $config_path, "<?php return array( 'layout' => array( 'templates/layout/updated-wrapper' ) );" );
		$config = Config::get();
		$this->assertEquals( [ 'templates/layout/updated-wrapper' ], $config['layout'] );
		$this->assertEquals( [ 'templates/layout/updated-footer' ], $config['footer'] );

		file_exists( $config_path ) && unlink( $config_path );
	}
}

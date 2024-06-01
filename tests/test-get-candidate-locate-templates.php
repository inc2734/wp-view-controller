<?php
use Inc2734\WP_View_Controller\Helper;

class Inc2734_WP_View_Controller_Candidate_Locate_Templates_Test extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		new \Inc2734\WP_View_Controller\Bootstrap();
	}

	public function tear_down() {
		parent::tear_down();
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function get_wrapper_templates() {
		$root    = untrailingslashit( sys_get_temp_dir() ) . '/root-templates';
		$wrapper = $root . '/wrapper';
		$file    = $wrapper . '/wrapper.php';
		$file2   = $wrapper . '/wrapper2.php';
		file_exists( $file ) && unlink( $file );
		file_exists( $file2 ) && unlink( $file2 );
		is_dir( $wrapper ) && rmdir( $wrapper );
		is_dir( $root ) && rmdir( $root );
		wp_mkdir_p( $wrapper );
		file_put_contents( $file, 'wrapper' );
		file_put_contents( $file2, 'wrapper' );

		add_filter(
			'inc2734_wp_view_controller_template_part_root_hierarchy',
			function( $hierarchy ) {
				$hierarchy[] = sys_get_temp_dir();
				return $hierarchy;
			}
		);

		add_filter(
			'inc2734_wp_view_controller_config',
			function( $config ) {
				$config['layout'] = [ 'root-templates/wrapper' ];
				return $config;
			}
		);

		$wrapper_templates = Helper::get_wrapper_templates();
		asort( $wrapper_templates );
		$this->assertSame(
			[
				'wrapper'  => 'wrapper',
				'wrapper2' => 'wrapper2',
			],
			$wrapper_templates
		);

		file_exists( $file ) && unlink( $file );
		file_exists( $file2 ) && unlink( $file2 );
		is_dir( $wrapper ) && rmdir( $wrapper );
		is_dir( $root ) && rmdir( $root );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function get_header_templates() {
		$root    = untrailingslashit( sys_get_temp_dir() ) . '/root-templates';
		$header  = $root . '/header';
		$file    = $header . '/header.php';
		$file2   = $header . '/header2.php';
		file_exists( $file ) && unlink( $file );
		file_exists( $file2 ) && unlink( $file2 );
		is_dir( $header ) && rmdir( $header );
		is_dir( $root ) && rmdir( $root );
		wp_mkdir_p( $header );
		file_put_contents( $file, 'header' );
		file_put_contents( $file2, 'header2' );

		add_filter(
			'inc2734_wp_view_controller_template_part_root_hierarchy',
			function( $hierarchy ) {
				$hierarchy[] = sys_get_temp_dir();
				return $hierarchy;
			}
		);

		add_filter(
			'inc2734_wp_view_controller_config',
			function( $config ) {
				$config['header'] = [ 'root-templates/header' ];
				return $config;
			}
		);

		$header_templates = Helper::get_header_templates();
		asort( $header_templates );
		$this->assertSame(
			[
				'header'  => 'header',
				'header2' => 'header2',
			],
			$header_templates
		);

		file_exists( $file ) && unlink( $file );
		file_exists( $file2 ) && unlink( $file2 );
		is_dir( $header ) && rmdir( $header );
		is_dir( $root ) && rmdir( $root );
	}
}

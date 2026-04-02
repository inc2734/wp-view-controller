<?php
class Inc2734_WP_View_Controller_Template_Tag_Test_Helper {

	use \Inc2734\WP_View_Controller\App\Contract\Template_Tag;

	public static $get_completed_hierarchy_count = 0;

	public static $hierarchy = array();

	public static function reset() {
		static::$get_completed_hierarchy_count = 0;
		static::$hierarchy                    = array();
	}

	public static function get_completed_hierarchy( $slug = null, $name = null ) {
		++static::$get_completed_hierarchy_count;
		return static::$hierarchy;
	}
}

class Inc2734_WP_View_Controller_Template_Part_Test_Helper extends \Inc2734\WP_View_Controller\App\Template_Part {

	public static $init_template_args_count = 0;

	public static $reset_template_args_count = 0;

	public static function reset() {
		static::$init_template_args_count  = 0;
		static::$reset_template_args_count = 0;
	}

	protected static function _init_template_args( $vars ) {
		++static::$init_template_args_count;
		parent::_init_template_args( $vars );
	}

	protected static function _reset_template_args() {
		++static::$reset_template_args_count;
		parent::_reset_template_args();
	}
}

class Inc2734_WP_View_Controller_Template_Part_Test extends WP_UnitTestCase {

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
	public function args() {
		add_filter(
			'inc2734_wp_view_controller_get_template_part_args',
			function( $args ) {
				$args['slug'] = 'template2';
				$args['name'] = 'name2';
				$args['vars'] = [ 'key' => 'value2' ];
				return $args;
			}
		);

		add_action(
			'inc2734_wp_view_controller_get_template_part_pre_render',
			function( $args ) {
				$this->assertEquals( 'template2', $args['slug'] );
				$this->assertEquals( 'name2', $args['name'] );
				$this->assertEquals( 'value2', $args['vars']['key'] );
			}
		);

		add_action( 'inc2734_wp_view_controller_get_template_part_template2-name2', '__return_true' );
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name', [ 'key' => 'value' ] );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function context() {
		add_filter(
			'inc2734_wp_view_controller_get_template_part_args',
			function( $args ) {
				$args['vars']['_context'] = 'fuga';
				return $args;
			}
		);

		add_action(
			'inc2734_wp_view_controller_get_template_part_pre_render',
			function( $args ) {
				if ( 'template' === $args['slug'] ) {
					$this->assertSame( null, $args['vars']['_context'] );
				}

				if ( 'template2' === $args['slug'] ) {
					$this->assertEquals( 'foo', $args['vars']['_context'] );
				}
			}
		);

		add_action( 'inc2734_wp_view_controller_get_template_part_template-name', '__return_true' );
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name' );

		add_action( 'inc2734_wp_view_controller_get_template_part_template2', '__return_true' );
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template2', null, [ '_context' => 'foo' ] );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function template_name() {
		add_filter(
			'inc2734_wp_view_controller_get_template_part_args',
			function( $args ) {
				$args['vars']['_name'] = 'fuga';
				return $args;
			}
		);

		add_action(
			'inc2734_wp_view_controller_get_template_part_pre_render',
			function( $args ) {
				if ( 'template' === $args['slug'] ) {
					$this->assertEquals( 'name', $args['vars']['_name'] );
				}

				if ( 'template2' === $args['slug'] ) {
					$this->assertEquals( null, $args['vars']['_name'] );
				}
			}
		);

		add_action( 'inc2734_wp_view_controller_get_template_part_template-name', '__return_true' );
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name' );

		add_action( 'inc2734_wp_view_controller_get_template_part_template2', '__return_true' );
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template2', null );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function template_part_root_hierarchy() {
		$root = untrailingslashit( sys_get_temp_dir() ) . '/template-parts';
		$file = $root . '/template-name.php';
		file_exists( $file ) && unlink( $file );
		is_dir( $root ) && rmdir( $root );
		mkdir( $root );
		file_put_contents( $file, 'hierarchy-test' );

		$root2 = untrailingslashit( sys_get_temp_dir() ) . '/template-parts2';
		$file2 = $root2 . '/template-name.php';
		file_exists( $file2 ) && unlink( $file2 );
		is_dir( $root2 ) && rmdir( $root2 );
		mkdir( $root2 );
		file_put_contents( $file2, 'hierarchy-test2' );

		add_filter(
			'inc2734_wp_view_controller_template_part_root_hierarchy',
			function( $hierarchy ) use ( $root, $root2 ) {
				$hierarchy[] = $root;
				$hierarchy[] = $root2;
				return $hierarchy;
			}
		);

		ob_start();
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name' );
		$this->assertEquals( 'hierarchy-test', ob_get_clean() );

		file_exists( $file ) && unlink( $file );
		ob_start();
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name' );
		$this->assertEquals( 'hierarchy-test2', ob_get_clean() );

		file_exists( $file2 ) && unlink( $file2 );
		ob_start();
		add_action( 'inc2734_wp_view_controller_get_template_part_template-name', '__return_true' );
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name' );
		$this->assertEquals( '', ob_get_clean() );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function defined_html() {
		add_action(
			'inc2734_wp_view_controller_get_template_part_template-name',
			function() {
				echo 'template-name';
			}
		);

		add_action(
			'inc2734_wp_view_controller_get_template_part_template-name',
			function() {
				echo '2-template-name';
			}
		);

		add_action(
			'inc2734_wp_view_controller_get_template_part_template',
			function() {
				echo 'template';
			}
		);

		ob_start();
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name' );
		$this->assertEquals( 'template-name2-template-name', ob_get_clean() );

		add_filter(
			'inc2734_wp_view_controller_template_part_render',
			function( $html, $slug, $name ) {
				if ( 'template' === $slug && 'name' === $name ) {
					return '4-template-name';
				}
			},
			10,
			3
		);

		ob_start();
		Inc2734\WP_View_Controller\Helper::get_template_part( 'template', 'name' );
		$this->assertEquals( '4-template-name', ob_get_clean() );
	}

	/**
	 * @test
	 */
	public function locate_template__get_completed_hierarchy_is_resolved_once_when_slug_is_provided() {
		$root = untrailingslashit( sys_get_temp_dir() ) . '/locate-template-root';
		$file = $root . '/template-name.php';
		file_exists( $file ) && unlink( $file );
		is_dir( $root ) && rmdir( $root );
		wp_mkdir_p( $root );
		file_put_contents( $file, 'template-name' );

		Inc2734_WP_View_Controller_Template_Tag_Test_Helper::reset();
		Inc2734_WP_View_Controller_Template_Tag_Test_Helper::$hierarchy = array( $root );

		$template_names = array(
			'template-missing.php',
			'template-name.php',
		);
		$cache_key      = crc32( implode( ':', $template_names ) );
		wp_cache_delete( $cache_key, 'inc2734/wp-view-controller/locate_template' );

		$located = Inc2734_WP_View_Controller_Template_Tag_Test_Helper::locate_template(
			$template_names,
			false,
			true,
			'template',
			'name'
		);

		$this->assertSame( $file, $located );
		$this->assertSame( 1, Inc2734_WP_View_Controller_Template_Tag_Test_Helper::$get_completed_hierarchy_count );

		Inc2734_WP_View_Controller_Template_Tag_Test_Helper::reset();
		$located = Inc2734_WP_View_Controller_Template_Tag_Test_Helper::locate_template(
			$template_names,
			false,
			true,
			'template',
			'name'
		);

		$this->assertSame( $file, $located );
		$this->assertSame( 0, Inc2734_WP_View_Controller_Template_Tag_Test_Helper::$get_completed_hierarchy_count );

		wp_cache_delete( $cache_key, 'inc2734/wp-view-controller/locate_template' );
		file_exists( $file ) && unlink( $file );
		is_dir( $root ) && rmdir( $root );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function render__skips_template_arg_setup_when_vars_are_empty() {
		$root = untrailingslashit( sys_get_temp_dir() ) . '/template-part-render-empty';
		$file = $root . '/template-empty-name.php';
		file_exists( $file ) && unlink( $file );
		is_dir( $root ) && rmdir( $root );
		wp_mkdir_p( $root );
		file_put_contents( $file, "<?php echo get_query_var( 'key', 'template-empty-name' );" );

		add_filter(
			'inc2734_wp_view_controller_template_part_root_hierarchy',
			function( $hierarchy ) use ( $root ) {
				$hierarchy[] = $root;
				return $hierarchy;
			}
		);

		$template_names = array(
			'template-empty-name.php',
			'template-empty.php',
		);
		$cache_key      = crc32( implode( ':', $template_names ) );
		wp_cache_delete( $cache_key, 'inc2734/wp-view-controller/locate_template' );

		Inc2734_WP_View_Controller_Template_Part_Test_Helper::reset();

		ob_start();
		Inc2734_WP_View_Controller_Template_Part_Test_Helper::render( 'template-empty', 'name', array() );
		$this->assertSame( 'template-empty-name', ob_get_clean() );
		$this->assertSame( 0, Inc2734_WP_View_Controller_Template_Part_Test_Helper::$init_template_args_count );
		$this->assertSame( 0, Inc2734_WP_View_Controller_Template_Part_Test_Helper::$reset_template_args_count );

		wp_cache_delete( $cache_key, 'inc2734/wp-view-controller/locate_template' );
		file_exists( $file ) && unlink( $file );
		is_dir( $root ) && rmdir( $root );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function render__initializes_template_args_when_vars_are_present() {
		$root = untrailingslashit( sys_get_temp_dir() ) . '/template-part-render-vars';
		$file = $root . '/template-vars-name.php';
		file_exists( $file ) && unlink( $file );
		is_dir( $root ) && rmdir( $root );
		wp_mkdir_p( $root );
		file_put_contents( $file, "<?php echo get_query_var( 'wp_view_controller_test_key', 'template-vars-name' );" );

		add_filter(
			'inc2734_wp_view_controller_template_part_root_hierarchy',
			function( $hierarchy ) use ( $root ) {
				$hierarchy[] = $root;
				return $hierarchy;
			}
		);

		$template_names = array(
			'template-vars-name.php',
			'template-vars.php',
		);
		$cache_key      = crc32( implode( ':', $template_names ) );
		wp_cache_delete( $cache_key, 'inc2734/wp-view-controller/locate_template' );

		Inc2734_WP_View_Controller_Template_Part_Test_Helper::reset();

		ob_start();
		Inc2734_WP_View_Controller_Template_Part_Test_Helper::render(
			'template-vars',
			'name',
			array(
				'wp_view_controller_test_key' => 'value',
			)
		);
		$this->assertSame( 'value', ob_get_clean() );
		$this->assertSame( 1, Inc2734_WP_View_Controller_Template_Part_Test_Helper::$init_template_args_count );
		$this->assertSame( 1, Inc2734_WP_View_Controller_Template_Part_Test_Helper::$reset_template_args_count );
		$this->assertSame( null, get_query_var( 'wp_view_controller_test_key', null ) );

		wp_cache_delete( $cache_key, 'inc2734/wp-view-controller/locate_template' );
		file_exists( $file ) && unlink( $file );
		is_dir( $root ) && rmdir( $root );
	}
}

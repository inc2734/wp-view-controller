<?php
class Inc2734_WP_View_Controller_View_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();
		include_once( __DIR__ . '/../src/wp-view-controller.php' );
		new Inc2734_WP_View_Controller();

		global $wp_rewrite;
		parent::setup();

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( '/%post_id%/' );

		$this->author_id     = $this->factory->user->create();
		$this->post_ids      = $this->factory->post->create_many( 20, [ 'post_author' => $this->author_id ] );
		$this->front_page_id = $this->factory->post->create( [ 'post_type' => 'page', 'post_title' => 'HOME' ] );
		$this->blog_page_id  = $this->factory->post->create( [ 'post_type' => 'page', 'post_title' => 'BLOG' ] );
		$this->tag_id        = $this->factory->term->create( array( 'taxonomy' => 'post_tag' ) );
		$this->post_type     = rand_str( 12 );
		$this->taxonomy      = rand_str( 12 );

		register_post_type(
			$this->post_type,
			[
				'public'      => true ,
				'taxonomies'  => ['category'],
				'has_archive' => true
			]
		);

		register_taxonomy(
			$this->taxonomy,
			$this->post_type,
			[
				'public' => true,
			]
		);

		foreach( $this->post_ids as $post_id ) {
			wp_set_object_terms( $post_id, get_term( $this->tag_id, 'post_tag' )->slug, 'post_tag' );
		}

		create_initial_taxonomies();
		$wp_rewrite->flush_rules();
	}

	public function tearDown() {
		parent::tearDown();

		_unregister_post_type( $this->post_type );
		_unregister_taxonomy( $this->taxonomy, $this->post_type );

		$static_view_directory = get_template_directory() . '/templates/view/static';
		system( 'chmod -R 755 ' . $static_view_directory );
		system( 'rm -R ' . $static_view_directory );
	}

	protected function _create_static_view_dir( $subdir ) {
		$static_view_directory = get_template_directory() . '/templates/view/static/' . trim( $subdir, '/' );
		if ( ! is_dir( $static_view_directory ) ) {
			mkdir( $static_view_directory, 0755, true);
		}
		chmod( $static_view_directory, 0755 );
		if ( is_dir( $static_view_directory ) ) {
			return untrailingslashit( $static_view_directory );
		}
	}

	protected function _create_static_view_template( $subdir, $filename ) {
		$static_view_directory = $this->_create_static_view_dir( trim( $subdir, '/' ) );
		$static_view_template  = $static_view_directory . '/' . trim( $filename, '/' ) . '.php';
		if ( is_writable( $static_view_directory ) ) {
			file_put_contents( $static_view_template, '-' );
			return $static_view_template;
		}
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__category() {
		$View = new Inc2734_WP_View_Controller_View();
		$category = get_terms( 'category' )[0];
		$this->go_to( get_term_link( $category ) );

		$static_view_template = $this->_create_static_view_template( 'category', $category->slug );
		$this->assertEquals(
			'templates/view/static/category/' . $category->slug,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( 'category/' . $category->slug, 'index' );
		$this->assertEquals(
			'templates/view/static/category/' . $category->slug . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_tag() {
		$View = new Inc2734_WP_View_Controller_View();
		$post_tag = get_terms( 'post_tag' )[0];
		$this->go_to( get_term_link( $post_tag ) );

		$static_view_template = $this->_create_static_view_template( 'tag', $post_tag->slug );
		$this->assertEquals(
			'templates/view/static/tag/' . $post_tag->slug,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( 'tag/' . $post_tag->slug, 'index' );
		$this->assertEquals(
			'templates/view/static/tag/' . $post_tag->slug . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__year() {
		$View = new Inc2734_WP_View_Controller_View();
		$newest_post = get_post( $this->post_ids[0] );
		$year = date( 'Y', strtotime( $newest_post->post_date ) );
		$this->go_to( get_year_link( $year ) );

		$static_view_template = $this->_create_static_view_template( 'date', $year );
		$this->assertEquals(
			'templates/view/static/date/' . $year,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( 'date/' . $year, 'index' );
		$this->assertEquals(
			'templates/view/static/date/' . $year . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__month() {
		$View = new Inc2734_WP_View_Controller_View();
		$newest_post = get_post( $this->post_ids[0] );
		$year  = date( 'Y', strtotime( $newest_post->post_date ) );
		$month = date( 'm', strtotime( $newest_post->post_date ) );
		$this->go_to( get_month_link( $year, $month ) );

		$static_view_template = $this->_create_static_view_template( 'date/' . $year, $month );
		$this->assertEquals(
			'templates/view/static/date/' . $year . '/' . $month,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( 'date/' . $year . '/' . $month, 'index' );
		$this->assertEquals(
			'templates/view/static/date/' . $year . '/' . $month . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__day() {
		$View = new Inc2734_WP_View_Controller_View();
		$newest_post = get_post( $this->post_ids[0] );
		$year  = date( 'Y', strtotime( $newest_post->post_date ) );
		$month = date( 'm', strtotime( $newest_post->post_date ) );
		$day   = date( 'd', strtotime( $newest_post->post_date ) );
		$this->go_to( get_day_link( $year, $month, $day ) );

		$static_view_template = $this->_create_static_view_template( 'date/' . $year . '/' . $month, $day );
		$this->assertEquals(
			'templates/view/static/date/' . $year . '/' . $month . '/' . $day,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( 'date/' . $year . '/' . $month . '/' . $day, 'index' );
		$this->assertEquals(
			'templates/view/static/date/' . $year . '/' . $month . '/' . $day . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__author() {
		$View = new Inc2734_WP_View_Controller_View();
		$newest_post = get_post( $this->post_ids[0] );
		$user_nicename = get_the_author_meta( 'user_nicename', $newest_post->post_author );
		$this->go_to( get_author_posts_url( $newest_post->post_author ) );

		$static_view_template = $this->_create_static_view_template( 'author', $user_nicename );
		$this->assertEquals(
			'templates/view/static/author/' . $user_nicename,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( 'author/' . $user_nicename, 'index' );
		$this->assertEquals(
			'templates/view/static/author/' . $user_nicename . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__single_post() {
		$View = new Inc2734_WP_View_Controller_View();
		$newest_post = get_post( $this->post_ids[0] );
		$categories = get_the_category( $newest_post );
		$this->go_to( get_permalink( $newest_post ) );

		$static_view_template = $this->_create_static_view_template( '', $this->post_ids[0] );
		$this->assertEquals(
			'templates/view/static/' . $this->post_ids[0],
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( $this->post_ids[0], 'index' );
		$this->assertEquals(
			'templates/view/static/' . $this->post_ids[0] . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__single_custom_post() {
		$View = new Inc2734_WP_View_Controller_View();
		$custom_post_type_id = $this->factory->post->create( [ 'post_type' => $this->post_type ] );
		$custom_post = get_post( $custom_post_type_id );
		$this->go_to( get_permalink( $custom_post_type_id ) );
		$post_type_object = get_post_type_object( $custom_post->post_type );

		$static_view_template = $this->_create_static_view_template( $post_type_object->name, $custom_post->post_name );
		$this->assertEquals(
			'templates/view/static/' . $post_type_object->name . '/' . $custom_post->post_name,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( $post_type_object->name . '/' . $custom_post->post_name, 'index' );
		$this->assertEquals(
			'templates/view/static/' . $post_type_object->name . '/' . $custom_post->post_name . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_type_archive_no_post() {
		$View = new Inc2734_WP_View_Controller_View();
		$this->go_to( get_post_type_archive_link( $this->post_type ) );
		$this->assertFalse( get_post_type() );
		$post_type_object = get_post_type_object( $this->post_type );

		$static_view_template = $this->_create_static_view_template( '', $post_type_object->name );
		$this->assertEquals(
			'templates/view/static/' . $post_type_object->name,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( $post_type_object->name, 'index' );
		$this->assertEquals(
			'templates/view/static/' . $post_type_object->name . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_type_archive_have_posts() {
		$View = new Inc2734_WP_View_Controller_View();
		$custom_post_type_id = $this->factory->post->create( [ 'post_type' => $this->post_type ] );
		$this->go_to( get_post_type_archive_link( $this->post_type ) );
		$post_type_object = get_post_type_object( $this->post_type );
		$this->assertNotFalse( get_post_type() );

		$static_view_template = $this->_create_static_view_template( '', $post_type_object->name );
		$this->assertEquals(
			'templates/view/static/' . $post_type_object->name,
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );

		$static_view_template = $this->_create_static_view_template( $post_type_object->name, 'index' );
		$this->assertEquals(
			'templates/view/static/' . $post_type_object->name . '/index',
			$View->get_static_view_template_name()
		);
		unlink( $static_view_template );
	}
}

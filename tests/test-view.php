<?php
class Inc2734_WP_View_Controller_View_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();
		new \Inc2734\WP_View_Controller\Bootstrap();

		global $wp_rewrite;
		parent::setup();

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( '/%postname%/' );

		$this->category_a_id = $this->factory->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Category A',
			]
		);

		$this->category_b_id = $this->factory->term->create(
			[
				'taxonomy' => 'category',
				'name'     => 'Category B',
			]
		);

		$this->tag_a_id = $this->factory->term->create(
			[
				'taxonomy' => 'post_tag',
				'name'     => 'Tag A',
			]
		);

		$this->tag_b_id = $this->factory->term->create(
			[
				'taxonomy' => 'post_tag',
				'name'     => 'Tag B',
			]
		);

		$this->author_a_id = $this->factory->user->create( [ 'user_login' => 'User A' ] );
		$this->author_b_id = $this->factory->user->create( [ 'user_login' => 'User B' ] );

		$this->post_2000_01_01_id = $this->factory->post->create(
			[
				'post_title'  => 'Post A',
				'post_name'   => 'post-a',
				'post_date'   => '2000/01/01 00:00:00',
				'post_author' => $this->author_a_id,
			]
		);

		$this->post_2000_01_02_id = $this->factory->post->create(
			[
				'post_title'  => 'Post B',
				'post_name'   => 'post-b',
				'post_date'   => '2000/01/02 00:00:00',
				'post_author' => $this->author_b_id,
			]
		);

		$this->post_2000_02_01_id = $this->factory->post->create(
			[
				'post_title'  => 'Post C',
				'post_name'   => 'post-c',
				'post_date'   => '2000/02/01 00:00:00',
				'post_author' => $this->author_a_id,
			]
		);

		$this->post_2001_01_01_id = $this->factory->post->create(
			[
				'post_title'  => 'Post D',
				'post_name'   => 'post-d',
				'post_date'   => '2001/01/01 00:00:00',
				'post_author' => $this->author_a_id,
			]
		);

		$this->post_type          = 'news';
		$this->post_type_no       = 'no';
		$this->post_type_no_index = 'no-index';
		$this->taxonomy           = 'news-category';

		register_post_type(
			$this->post_type,
			[
				'public'      => true ,
				'taxonomies'  => [ $this->taxonomy ],
				'has_archive' => true
			]
		);

		register_post_type(
			$this->post_type_no,
			[
				'public'      => true ,
				'taxonomies'  => [],
				'has_archive' => true
			]
		);

		register_post_type(
			$this->post_type_no_index,
			[
				'public'      => true ,
				'taxonomies'  => [],
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

		foreach(
			[
				$this->post_2000_01_01_id,
				$this->post_2000_01_02_id,
				$this->post_2000_02_01_id,
				$this->post_2001_01_01_id,
			] as $post_id
		) {
			wp_set_object_terms( $post_id, get_term( $this->category_a_id, 'category' )->slug, 'category' );
			wp_set_object_terms( $post_id, get_term( $this->category_b_id, 'category' )->slug, 'category' );
			wp_set_object_terms( $post_id, get_term( $this->tag_a_id, 'post_tag' )->slug, 'post_tag' );
			wp_set_object_terms( $post_id, get_term( $this->tag_b_id, 'post_tag' )->slug, 'post_tag' );
		}

		create_initial_taxonomies();
		$wp_rewrite->flush_rules();

		$this->static_view_directory = get_template_directory() . '/templates/static';
	}

	public function tearDown() {
		parent::tearDown();

		_unregister_post_type( $this->post_type );
		_unregister_taxonomy( $this->taxonomy, $this->post_type );
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__category() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$category = get_term( $this->category_a_id, 'category' );
		$this->go_to( get_term_link( $category ) );

		$this->assertEquals(
			'templates/static/category/category-a',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__category__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$category = get_term( $this->category_b_id, 'category' );
		$this->go_to( get_term_link( $category ) );

		$this->assertEquals(
			'templates/static/category/category-b/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_tag() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post_tag = get_term( $this->tag_a_id, 'post_tag' );
		$this->go_to( get_term_link( $post_tag ) );

		$this->assertEquals(
			'templates/static/tag/tag-a',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_tag__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post_tag = get_term( $this->tag_b_id, 'post_tag' );
		$this->go_to( get_term_link( $post_tag ) );

		$this->assertEquals(
			'templates/static/tag/tag-b/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__year() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2001_01_01_id );
		$year = date( 'Y', strtotime( $post->post_date ) );
		$this->go_to( get_year_link( $year ) );

		$this->assertEquals(
			'templates/static/2001',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__year__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_01_id );
		$year = date( 'Y', strtotime( $post->post_date ) );
		$this->go_to( get_year_link( $year ) );

		$this->assertEquals(
			'templates/static/2000/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__month() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_02_01_id );
		$year  = date( 'Y', strtotime( $post->post_date ) );
		$month = date( 'm', strtotime( $post->post_date ) );
		$this->go_to( get_month_link( $year, $month ) );

		$this->assertEquals(
			'templates/static/2000/02',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__month__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_01_id );
		$year  = date( 'Y', strtotime( $post->post_date ) );
		$month = date( 'm', strtotime( $post->post_date ) );
		$this->go_to( get_month_link( $year, $month ) );

		$this->assertEquals(
			'templates/static/2000/01/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__day() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_01_id );
		$year  = date( 'Y', strtotime( $post->post_date ) );
		$month = date( 'm', strtotime( $post->post_date ) );
		$day   = date( 'd', strtotime( $post->post_date ) );
		$this->go_to( get_day_link( $year, $month, $day ) );

		$this->assertEquals(
			'templates/static/2000/01/01',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__day__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_02_id );
		$year  = date( 'Y', strtotime( $post->post_date ) );
		$month = date( 'm', strtotime( $post->post_date ) );
		$day   = date( 'd', strtotime( $post->post_date ) );
		$this->go_to( get_day_link( $year, $month, $day ) );

		$this->assertEquals(
			'templates/static/2000/01/02/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__author() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_02_id );
		$this->go_to( get_author_posts_url( $post->post_author ) );

		$this->assertEquals(
			'templates/static/author/user-b',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__author__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_01_id );
		$this->go_to( get_author_posts_url( $post->post_author ) );

		$this->assertEquals(
			'templates/static/author/user-a/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__single_post() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_01_id );
		$this->go_to( get_permalink( $post ) );

		$this->assertEquals(
			'templates/static/post-a',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__single_post__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$post = get_post( $this->post_2000_01_02_id );
		$this->go_to( get_permalink( $post ) );

		$this->assertEquals(
			'templates/static/post-b/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__single_custom_post() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$custom_post_type_id = $this->factory->post->create(
			[
				'post_type'  => $this->post_type,
				'post_title' => 'News A'
			]
		);
		$this->go_to( get_permalink( $custom_post_type_id ) );

		$this->assertEquals(
			'templates/static/news/news-a',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__single_custom_post__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$custom_post_type_id = $this->factory->post->create(
			[
				'post_type'  => $this->post_type,
				'post_title' => 'News B'
			]
		);
		$this->go_to( get_permalink( $custom_post_type_id ) );

		$this->assertEquals(
			'templates/static/news/news-b/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_type_archive_no_post() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$this->go_to( get_post_type_archive_link( $this->post_type_no ) );
		$this->assertFalse( get_post_type() );

		$this->assertEquals(
			'templates/static/no',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_type_archive_no_post__index() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$this->go_to( get_post_type_archive_link( $this->post_type_no_index ) );
		$this->assertFalse( get_post_type() );

		$this->assertEquals(
			'templates/static/no-index/index',
			$View->get_static_view_template_name()
		);
	}

	/**
	 * @test
	 */
	public function get_static_view_template_name__post_type_archive_have_posts() {
		$View = new Inc2734\WP_View_Controller\App\View();
		$this->factory->post->create( [ 'post_type' => $this->post_type ] );
		$this->go_to( get_post_type_archive_link( $this->post_type ) );
		$this->assertNotFalse( get_post_type() );

		$this->assertEquals(
			'templates/static/news/index',
			$View->get_static_view_template_name()
		);
	}
}

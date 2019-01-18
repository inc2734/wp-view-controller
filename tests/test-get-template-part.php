<?php
class Inc2734_WP_View_Controller_Template_Part_Test extends WP_UnitTestCase {

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
	public function set_var() {
		global $wp_query;
		$Template_Part = new Inc2734\WP_View_Controller\App\Template_Part( 'template' );
		$Template_Part->set_var( '_name', 'value' );
		$this->assertEquals( 'value', $wp_query->get( '_name' ) );
	}

	/**
	 * @test
	 */
	public function set_vars() {
		global $wp_query;
		$Template_Part = new Inc2734\WP_View_Controller\App\Template_Part( 'template' );
		$Template_Part->set_vars( array(
			'_name-1' => 'value-1',
			'_name-2' => 'value-2',
		) );
		$this->assertEquals( 'value-1', $wp_query->get( '_name-1' ) );
		$this->assertEquals( 'value-2', $wp_query->get( '_name-2' ) );
	}

	/**
	 * @test
	 */
	public function render() {
		global $wp_query;
		$Template_Part = new Inc2734\WP_View_Controller\App\Template_Part( 'template' );
		$Template_Part->set_var( '_name', 'value' );
		$Template_Part->render();
		$this->assertEquals( '', $wp_query->get( '_name' ) );
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 */
	public function args() {
		add_filter(
			'inc2734_view_controller_get_template_part_args',
			function( $args ) {
				$args['slug'] = 'template2';
				$args['name'] = 'name2';
				$args['vars'] = [ 'key' => 'value2' ];
				return $args;
			}
		);

		add_action(
			'inc2734_view_controller_get_template_part_pre_render',
			function( $args ) {
				$this->assertEquals( 'template2', $args['slug'] );
				$this->assertEquals( 'name2', $args['name'] );
				$this->assertEquals( 'value2', $args['vars']['key'] );
			}
		);

		Inc2734\WP_View_Controller\Helper\get_template_part( 'template', 'name', [ 'key' => 'value' ] );
	}
}

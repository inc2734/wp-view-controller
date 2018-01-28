<?php
class Inc2734_WP_View_Controller_Template_Part_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();
		new Inc2734\WP_View_Controller\View_Controller();
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
}

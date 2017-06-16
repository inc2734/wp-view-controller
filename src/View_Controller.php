<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller;

/**
 * View controller
 */
class View_Controller {

	/**
	 * @var Inc2734_WP_View_Controller
	 */
	protected $View_Controller;

	public function __construct() {
		include_once( __DIR__ . '/wp-view-controller.php' );
		$this->View_Controller = new \Inc2734_WP_View_Controller();
	}

	/**
	 * Rendering the page
	 *
	 * @param string $view view template path
	 * @param string $view_suffix view template suffix
	 * @return void
	 */
	public function render( $view, $view_suffix = '' ) {
		$this->View_Controller->render( $view, $view_suffix );
	}

	/**
	 * Sets the layout template
	 *
	 * @param string $layout layout template path
	 * @return void
	 */
	public function layout( $layout ) {
		$this->View_Controller->layout( $layout );
	}
}

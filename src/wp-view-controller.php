<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

$includes = array(
	'/app/model',
	'/app/view',
	'/app/template-tags',
);
foreach ( $includes as $include ) {
	foreach ( glob( __DIR__ . $include . '/*.php' ) as $file ) {
		require_once( $file );
	}
}

/**
 * View controller
 */
class Inc2734_WP_View_Controller {

	/**
	 * View object
	 *
	 * @var View
	 */
	protected $view;

	public function __construct() {
		$this->view = new Inc2734_WP_View_Controller_View();
	}

	/**
	 * Rendering the page
	 *
	 * @param string $view view template path
	 * @param string $view_suffix view template suffix
	 * @return void
	 */
	public function render( $view, $view_suffix = '' ) {
		$this->view->render( $view, $view_suffix );
	}

	/**
	 * Sets the layout template
	 *
	 * @param string $layout layout template path
	 * @return void
	 */
	public function layout( $layout ) {
		$this->view->layout( $layout );
	}
}

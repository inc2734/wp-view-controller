<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller;

use Inc2734\WP_View_Controller\App\Loader;
use Inc2734\WP_View_Controller\App\View;

/**
 * Old bootstrap class.
 *
 * @deprecated
 */
class View_Controller {

	/**
	 * View object
	 *
	 * @var View
	 */
	protected $view;

	public function __construct() {
		$this->view = new View();
		error_log( 'Inc2734\WP_View_Controller\View_Controller class is deprecated. You should use Inc2734\WP_View_Controller\Bootstrap class.' );
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

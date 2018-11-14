<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller;

use Inc2734\WP_View_Controller\App\Loader;
use Inc2734\WP_View_Controller\App\View;

class Bootstrap {

	/**
	 * View object
	 *
	 * @var View
	 */
	protected static $view;

	public function __construct() {
		Loader::load_template_tags();
		Loader::load_setup_files();
		static::_set_view();
	}

	/**
	 * Rendering the page
	 *
	 * @param string $view view template path
	 * @param string $view_suffix view template suffix
	 * @return void
	 */
	public static function render( $view, $view_suffix = '' ) {
		static::_set_view();
		static::$view->render( $view, $view_suffix );
	}

	/**
	 * Sets the layout template
	 *
	 * @param string $layout layout template path
	 * @return void
	 */
	public static function layout( $layout ) {
		static::_set_view();
		static::$view->layout( $layout );
	}

	/**
	 * Set View object
	 *
	 * @return void
	 */
	protected static function _set_view() {
		if ( ! static::$view ) {
			static::$view = new View();
		}
	}
}

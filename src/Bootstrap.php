<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller;

use Inc2734\WP_View_Controller\App\View;

class Bootstrap {

	/**
	 * View object.
	 *
	 * @var View
	 */
	protected static $view;

	/**
	 * Constructor.
	 */
	public function __construct() {
		load_textdomain( 'inc2734-wp-view-controller', __DIR__ . '/languages/' . get_locale() . '.mo' );

		include_once( __DIR__ . '/deprecated/Helper.php' );
		include_once( __DIR__ . '/setup/comments-template.php' );
		include_once( __DIR__ . '/setup/custom-page-template-selector.php' );
		include_once( __DIR__ . '/setup/debug-template-overwrite.php' );
		include_once( __DIR__ . '/setup/template-loader.php' );

		static::_set_view();
	}

	/**
	 * Rendering the page.
	 *
	 * @param string $view view   The template path.
	 * @param string $view_suffix The view template suffix.
	 * @return void
	 */
	public static function render( $view, $view_suffix = '' ) {
		static::_set_view();
		static::$view->render( $view, $view_suffix );
	}

	/**
	 * Sets the layout template.
	 *
	 * @param string $layout The layout template path.
	 * @return void
	 */
	public static function layout( $layout ) {
		static::_set_view();
		static::$view->layout( $layout );
	}

	/**
	 * Return layout arg.
	 *
	 * @return string
	 */
	public static function get_layout() {
		return static::$view->get_layout();
	}

	/**
	 * Return view arg.
	 *
	 * @return string
	 */
	public static function get_view() {
		return static::$view->get_view();
	}

	/**
	 * Return view_suffix arg.
	 *
	 * @return string
	 */
	public static function get_view_suffix() {
		return static::$view->get_view_suffix();
	}

	/**
	 * Set View object.
	 *
	 * @return void
	 */
	protected static function _set_view() {
		if ( ! static::$view ) {
			static::$view = new View();
		}
	}
}

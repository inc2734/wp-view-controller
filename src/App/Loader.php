<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\App;

class Loader {

	/**
	 * Load template tags
	 *
	 * @return void
	 */
	public static function load_template_tags() {
		static::load( __DIR__ . '/../template-tags' );
	}

	/**
	 * Load files for setup
	 *
	 * @return void
	 */
	public static function load_setup_files() {
		static::load( __DIR__ . '/../setup' );
	}

	/**
	 * Load files
	 *
	 * @param string $directory
	 * @return void
	 */
	protected static function load( $directory ) {
		foreach ( glob( $directory . '/*.php' ) as $file ) {
			require_once( $file );
		}
	}
}

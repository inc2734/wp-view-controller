<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\App;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Loader {

	/**
	 * Load helper function.
	 *
	 * @return void
	 */
	public static function load_deprecated() {
		static::load( __DIR__ . '/../deprecated' );
	}

	/**
	 * Load files for setup.
	 *
	 * @return void
	 */
	public static function load_setup_files() {
		static::load( __DIR__ . '/../setup' );
	}

	/**
	 * Load files.
	 *
	 * @param string $directory Target directory path.
	 * @return void
	 */
	protected static function load( $directory ) {
		$iterator = new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $iterator );

		foreach ( $iterator as $file ) {
			if ( ! $file->isFile() ) {
				continue;
			}

			if ( 'php' !== $file->getExtension() ) {
				continue;
			}

			include_once( realpath( $file->getPathname() ) );
		}
	}
}

<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller;

use Inc2734\WP_View_Controller\App\Contract;

class Helper {

	use Contract\Template_Tag;

	/**
	 * Return inc2734_wp_view_controller_debug.
	 *
	 * @return boolean
	 */
	public static function is_debug_mode() {
		$debug = defined( 'WP_DEBUG' ) ? WP_DEBUG : true;

		return apply_filters( 'inc2734_wp_view_controller_debug', $debug );
	}

	/**
	 * Return file header.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_file_data/
	 *
	 * @param string $file Full file path.
	 * @return string
	 */
	protected static function _get_file_data( $file ) {
		if ( ! file_exists( $file ) ) {
			return false;
		}

		// We don't need to write to the file, so just open for reading.
		$file_pointer = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $file_pointer, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $file_pointer );

		// Make sure we catch CR-only line endings.
		return str_replace( "\r", "\n", $file_data );
	}

	/**
	 * Return @version data.
	 *
	 * @param string $file Full file path.
	 * @return string|null
	 */
	public static function get_file_version( $file ) {
		// Make sure we catch CR-only line endings.
		$file_data = static::_get_file_data( $file );

		if ( ! $file_data ) {
			return false;
		}

		if ( preg_match( '/^[ \t\/*#@]*@version(.*)$/mi', $file_data, $match ) && $match[1] ) {
			return _cleanup_header_comment( $match[1] );
		}
	}

	/**
	 * Return renamed: data.
	 *
	 * @param string $file Full file path.
	 * @return array
	 */
	public static function get_file_renamed( $file ) {
		// Make sure we catch CR-only line endings.
		$file_data = static::_get_file_data( $file );

		if ( ! $file_data ) {
			return [];
		}

		if ( preg_match_all( '/^[ \t\/*#@]*renamed:(.*)$/mi', $file_data, $match ) && $match[1] ) {
			return array_map( '_cleanup_header_comment', $match[1] );
		}

		return [];
	}
}

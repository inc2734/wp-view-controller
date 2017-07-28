<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Config loader
 */
class Inc2734_WP_View_Controller_Config {

	/**
	 * Getting config value
	 *
	 * @param string $key the key of the config
	 * @return mixed
	 */
	public static function get( $key = null ) {
		$path = apply_filters(
			'inc2734_view_controller_config_path',
			untrailingslashit( __DIR__ ) . '/../config/config.php'
		);

		if ( ! file_exists( $path ) ) {
			return;
		}

		$config = include( $path );

		if ( is_null( $key ) ) {
			return $config;
		}

		if ( ! isset( $config[ $key ] ) ) {
			return;
		}

		return $config[ $key ];
	}
}

/**
 * Getting config value
 *
 * @param string $key the key of the config
 * @return mixed
 */
function wpvc_config( $key = null ) {
	return Inc2734_WP_View_Controller_Config::get( $key );
}

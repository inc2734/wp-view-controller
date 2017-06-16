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
	 * @param string $slug the slug of the config file. e.g. config/directory
	 * @param string $key the key of the config
	 * @return mixed
	 */
	public static function get( $slug, $key = null ) {
		$config_directory = apply_filters(
			'inc2734_view_controller_config_directory',
			untrailingslashit( __DIR__ ) . '/../config/'
		);
		$path = trailingslashit( $config_directory ) . $slug . '.php';

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

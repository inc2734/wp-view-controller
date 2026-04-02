<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\App;

class Config {

	/**
	 * Default config values.
	 *
	 * @var array
	 */
	protected const DEFAULT_CONFIG = array(
		'templates'     => array( '' ),
		'page-template' => array( 'page-templates' ),
		'layout'        => array( 'templates/layout/wrapper' ),
		'header'        => array( 'templates/layout/header' ),
		'sidebar'       => array( 'templates/layout/sidebar' ),
		'footer'        => array( 'templates/layout/footer' ),
		'view'          => array( 'templates/view' ),
		'static'        => array( 'templates/static' ),
	);

	/**
	 * Returns the legacy default config path used by the path filter.
	 *
	 * @return string
	 */
	protected static function _get_default_config_path() {
		return untrailingslashit( __DIR__ ) . '/../config/config.php';
	}

	/**
	 * Returns default config values.
	 *
	 * @return array
	 */
	protected static function _get_default_config() {
		return static::DEFAULT_CONFIG;
	}

	/**
	 * Getting config value.
	 *
	 * @param string $key The key of the config.
	 * @return mixed
	 */
	public static function get( $key = null ) {
		$default_config_path = static::_get_default_config_path();

		/*
		 * @deprecated Use inc2734_wp_view_controller_config whenever possible.
		 */
		$path = apply_filters(
			'inc2734_wp_view_controller_config_path',
			$default_config_path
		);

		if ( wp_normalize_path( $default_config_path ) === wp_normalize_path( $path ) ) {
			$config = static::_get_default_config();
		} else {
			if ( ! file_exists( $path ) ) {
				return;
			}

			$config = include $path;
		}

		$config = apply_filters( 'inc2734_wp_view_controller_config', $config );

		if ( is_null( $key ) ) {
			return $config;
		}

		if ( ! isset( $config[ $key ] ) ) {
			return;
		}

		return $config[ $key ];
	}
}

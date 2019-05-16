<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

use Inc2734\WP_View_Controller\App\Config;
use Inc2734\WP_View_Controller\App\Template_Part;

/**
 * Getting config value
 *
 * @param string $key the key of the config
 * @return mixed
 */
function config( $key = null ) {
	return \Inc2734\WP_View_Controller\Helper::config( $key );
}

/**
 * Load footer template
 *
 * @param string $name
 * @return void
 */
function get_footer_template( $name = 'footer' ) {
	\Inc2734\WP_View_Controller\Helper::get_footer_template( $name );
}

/**
 * This function like get_footer()
 *
 * @param string $name
 * @return void
 */
function get_footer( $name = null ) {
	\Inc2734\WP_View_Controller\Helper::get_footer( $name );
}

/**
 * Load header template
 *
 * @param string $name
 * @return void
 */
function get_header_template( $name = 'header' ) {
	\Inc2734\WP_View_Controller\Helper::get_header_template( $name );
}

/**
 * This function like get_header()
 *
 * @param string $name
 * @return void
 */
function get_header( $name = null ) {
	\Inc2734\WP_View_Controller\Helper::get_header( $name );
}

/**
 * Load sidebar template
 *
 * @param string $name
 * @return void
 */
function get_sidebar_template( $name = 'sidebar' ) {
	\Inc2734\WP_View_Controller\Helper::get_sidebar_template( $name );
}

/**
 * This function like get_sidebar()
 *
 * @param string $name
 * @return void
 */
function get_sidebar( $name = null ) {
	\Inc2734\WP_View_Controller\Helper::get_sidebar( $name );
}

/**
 * A template tag that is get_template_part() using variables
 *
 * @param string $slug
 * @param string $name
 * @param array $vars
 * @return void
 */
function get_template_part( $slug, $name = null, array $vars = [] ) {
	/**
	 * Backward compatibility
	 */
	if ( is_array( $name ) && is_array( $vars ) && empty( $vars ) ) {
		$vars = $name;
		$name = null;
	}

	\Inc2734\WP_View_Controller\Helper::get_template_part( $slug, $name, $vars );
}

/**
 * Load wrapper template
 *
 * @param string $name
 * @param array $args
 * @return void
 */
function get_wrapper_template( $name = 'wrapper', array $args = array() ) {
	\Inc2734\WP_View_Controller\Helper::get_wrapper_template( $name, $args );
}

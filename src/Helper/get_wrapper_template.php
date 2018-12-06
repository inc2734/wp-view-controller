<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

/**
 * Load wrapper template
 *
 * @param string $name
 * @param array $args
 * @return void
 */
function get_wrapper_template( $name = 'wrapper', array $args = array() ) {
	$template_name = locate_template( (array) config( 'layout' ), $name );

	if ( empty( $template_name ) ) {
		return;
	}

	get_template_part( $template_name, null, $args );
}

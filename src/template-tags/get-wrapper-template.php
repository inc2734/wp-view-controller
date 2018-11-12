<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Load wrapper template
 *
 * @param string $name
 * @param array $args
 * @return void
 */
function wpvc_get_wrapper_template( $name = 'wrapper', array $args = array() ) {
	$template_name = wpvc_locate_template( (array) wpvc_config( 'layout' ), $name );

	if ( empty( $template_name ) ) {
		return;
	}

	wpvc_get_template_part( $template_name, $args );
}

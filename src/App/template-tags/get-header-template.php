<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Load header template
 *
 * @param string $name
 * @return void
 */
function wpvc_get_header_template( $name = 'header' ) {
	$template_name = wpvc_locate_template( (array) wpvc_config( 'header' ), $name );

	if ( empty( $template_name ) ) {
		return;
	}

	get_template_part( $template_name );
}

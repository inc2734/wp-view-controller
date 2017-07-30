<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * This function like get_footer()
 *
 * @param string $name
 * @return void
 */
function wpvc_get_footer( $name = null ) {
	do_action( 'get_footer', $name );

	if ( '' !== $name ) {
		$template_name = wpvc_locate_template( (array) wpvc_config( 'templates' ), 'footer-' . $name );
	}

	if ( empty( $template_name ) ) {
		$template_name = wpvc_locate_template( (array) wpvc_config( 'templates' ), 'footer' );
	}

	if ( empty( $template_name ) ) {
		return;
	}

	get_template_part( $template_name );
}

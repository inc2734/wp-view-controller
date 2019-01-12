<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

/**
 * Load footer template
 *
 * @param string $name
 * @return void
 */
function get_footer_template( $name = 'footer' ) {
	$template_name = locate_template( (array) config( 'footer' ), $name );

	if ( empty( $template_name ) ) {
		return;
	}

	get_template_part( $template_name );
}

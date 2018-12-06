<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

/**
 * Return template name by $slug and $name
 *
 * @param string $slug
 * @param string $name
 * @return null|string Template name that can be used in get_template_part()
 */
function get_template_name( $slug, $name ) {
	$template_names = [];
	if ( $name ) {
		$template_names[] = $slug . '-' . $name . '.php';
	}
	$template_names[] = $slug . '.php';

	$template_path = \locate_template( $template_names, false );
	$template_name = '';
	if ( $template_path ) {
		if ( false !== strpos( $template_path, $slug . '-' . $name . '.php' ) ) {
			$template_name = $slug . '-' . $name;
		} else {
			$template_name = $slug;
		}
	}

	return $template_name;
}

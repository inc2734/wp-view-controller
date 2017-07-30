<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Locate template
 *
 * @param array $template_directory_slugs Template name that can be used in get_template_part()
 * @param string $template_name
 * @return null|string Template name that can be used in get_template_part()
 */
function wpvc_locate_template( $template_directory_slugs, $name ) {
	$template_directory_slugs = (array) $template_directory_slugs;

	foreach ( $template_directory_slugs as $slug ) {
		if ( locate_template( $slug . '/' . $name . '.php', false ) ) {
			return $slug . '/' . $name;
		}
	}
}

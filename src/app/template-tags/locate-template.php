<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Locate template
 *
 * @param array $directory_slugs Template name that can be used in get_template_part()
 * @param string $template_name
 * @return null|string Template name that can be used in get_template_part()
 */
function wpvc_locate_template( $directory_slugs, $name ) {
	$directory_slugs = (array) $directory_slugs;
	$name = str_replace( '.php', '', $name );

	if ( empty( $directory_slugs ) ) {
		if ( locate_template( $name . '.php', false ) ) {
			return $name;
		}
	}

	foreach ( $directory_slugs as $slug ) {
		if ( empty( $slug ) && locate_template( $name . '.php', false ) ) {
			return $name;
		}

		if ( locate_template( $slug . '/' . $name . '.php', false ) ) {
			return $slug . '/' . $name;
		}
	}
}

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
 * @param string $slug
 * @param string $name
 * @return null|string Template name that can be used in get_template_part()
 */
function wpvc_locate_template( $directory_slugs, $slug, $name = '' ) {
	$directory_slugs = (array) $directory_slugs;
	$slug = rtrim( $slug, '.php' );

	if ( empty( $directory_slugs ) ) {
		return wpvc_get_template_name( $slug, $name );
	}

	foreach ( $directory_slugs as $directory_slug ) {
		if ( $directory_slug ) {
			$slug = $directory_slug . '/' . $slug;
		}

		$template_name = wpvc_get_template_name( $slug, $name );
		if ( $template_name ) {
			return $template_name;
		}
	}
}

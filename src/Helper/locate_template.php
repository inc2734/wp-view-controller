<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

/**
 * Locate template that based in theme directory
 *
 * @param array $directory_slugs Template name that can be used in get_template_part()
 * @param string $slug
 * @param string $name
 * @return null|string Template name that can be used in get_template_part()
 */
function locate_template( $directory_slugs, $slug, $name = '' ) {
	$directory_slugs = (array) $directory_slugs;
	$slug = preg_replace( '|\.php$|', '', $slug );

	if ( empty( $directory_slugs ) ) {
		return get_template_name( $slug, $name );
	}

	foreach ( $directory_slugs as $directory_slug ) {
		if ( $directory_slug ) {
			$new_slug = $directory_slug . '/' . $slug;
		} else {
			$new_slug = $slug;
		}

		$template_name = get_template_name( $new_slug, $name );
		if ( $template_name ) {
			return $template_name;
		}
	}
}

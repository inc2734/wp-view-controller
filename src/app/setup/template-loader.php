<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * @see https://developer.wordpress.org/reference/hooks/type_template_hierarchy/
 */
add_action( 'after_setup_theme', function() {
	$types = array(
		'index',
		'404',
		'archive',
		'author',
		'category',
		'tag',
		'taxonomy',
		'date',
		'embed',
		'home',
		'frontpage',
		'page',
		'paged',
		'search',
		'single',
		'singular',
		'attachment',
	);

	foreach ( $types as $type ) {
		add_filter( "{$type}_template_hierarchy", function( $templates ) {
			$new_templates = $templates;
			foreach ( $templates as $template ) {
				$slug = wpvc_config( 'templates' );
				$new_templates[] = $slug . '/' . $template;
			}
			return $new_templates;
		} );
	}
} );

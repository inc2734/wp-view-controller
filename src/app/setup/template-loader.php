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
				$template_name = wpvc_locate_template( (array) wpvc_config( 'templates' ), basename( $template , '.php' ) );
				if ( is_null( $template_name ) ) {
					continue;
				}
				$new_templates[] = $template_name . '.php';
			}

			$default_template_index = array_search( 'index.php', $new_templates );
			if ( false !== $default_template_index ) {
				unset( $new_templates[ $default_template_index ] );
				$new_templates[] = 'index.php';
			}

			return array_unique( $new_templates );
		} );
	}
} );

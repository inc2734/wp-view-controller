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
				$_wp_page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
				if ( $_wp_page_template && 'default' !== $_wp_page_template ) {
					$template_name = wpvc_locate_template( (array) wpvc_config( 'page-templates' ), str_replace( '.php', '', $_wp_page_template ) );
					if ( is_null( $template_name ) ) {
						continue;
					}
					$new_templates[] = $template_name . '.php';
					continue;
				}

				$template_name = wpvc_locate_template( (array) wpvc_config( 'templates' ), str_replace( '.php', '', $template ) );
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

		add_filter( "{$type}_template", function( $template, $type, $templates ) {
			$custom_page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			if ( $custom_page_template ) {
				if ( $custom_page_template && 'default' !== $custom_page_template ) {
					if ( file_exists( get_theme_file_path( $custom_page_template ) ) ) {
						return $custom_page_template;
					}
				}
			}

			foreach ( $templates as $_template ) {
				$directory = (array) wpvc_config( 'templates' );
				$directory = array_merge( array( '' ), $directory );
				$template_name = wpvc_locate_template( $directory, $_template );
				if ( $template_name && get_theme_file_path( $template_name ) ) {
					return get_theme_file_path( $template_name . '.php' );
				}
			}

			return $template;
		}, 10, 3 );
	}

	add_filter( 'template_include', function( $template ) {
		if ( file_exists( get_theme_file_path( $template ) ) ) {
			return get_theme_file_path( $template );
		}
		return $template;
	} );
} );

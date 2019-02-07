<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_View_Controller\Helper;

/**
 * @see https://developer.wordpress.org/reference/hooks/type_template_hierarchy/
 */
add_action(
	'after_setup_theme',
	function() {
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
			add_filter(
				"{$type}_template_hierarchy",
				function( $templates ) use ( $type ) {
					$new_templates = $templates;

					foreach ( $templates as $template ) {
						if ( in_array( $type, [ 'frontpage', 'singular', 'single', 'page' ] ) ) {
							$_wp_page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
							if ( $_wp_page_template && 'default' !== $_wp_page_template ) {
								$new_templates = array_merge( [ $_wp_page_template ], $new_templates );
								continue;
							}
						}

						$template_name = Helper\locate_template( (array) Helper\config( 'templates' ), str_replace( '.php', '', $template ) );
						if ( $template_name ) {
							$new_templates[] = $template_name . '.php';
						}
					}

					$default_template_index = array_search( 'index.php', $new_templates );
					if ( false !== $default_template_index ) {
						unset( $new_templates[ $default_template_index ] );
						$new_templates[] = 'index.php';
					}

					return array_unique( $new_templates );
				}
			);
		}

		add_filter(
			'frontpage_template',
			function( $template ) {
				return is_home() ? '' : $template;
			}
		);

		add_filter(
			'template_include',
			function( $template ) {
				$filename = str_replace( trailingslashit( get_template_directory() ), '', $template );
				if ( is_child_theme() ) {
					$filename = str_replace( trailingslashit( get_stylesheet_directory() ), '', $filename );
				}

				$filtered_template = apply_filters( 'inc2734_wp_view_controller_controller', $template, $filename );

				return file_exists( $filtered_template ) ? $filtered_template : $template;
			}
		);
	}
);

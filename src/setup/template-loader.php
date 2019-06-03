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
						if ( 'frontpage' === $type ) {
							$_wp_page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
							if ( $_wp_page_template && 'default' !== $_wp_page_template ) {
								$new_templates = array_merge( [ $_wp_page_template ], $new_templates );
								continue;
							}
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

			/**
			 * Only relative paths are stored in $templates.
			 * Since only the files directly under the theme are checked, the extended root is also checked.
			 */
			add_filter(
				"{$type}_template",
				function( $template, $type, $templates ) {
					$located = Helper::locate_template( $templates, false );
					if ( $located ) {
						return $located;
					}

					$template_names = [];
					$hierarchy = Helper::config( 'templates' );
					foreach ( $hierarchy as $root ) {
						foreach ( $templates as $_template ) {
							$template_names = untrailingslashit( $root ) . '/' . $_template;
						}
					}
					if ( $template_names ) {
						$located = Helper::locate_template( $template_names, false );
						if ( $located ) {
							return $located;
						}
					}

					return $template;
				},
				10,
				3
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
				$filename  = $template;
				$hierarchy = Helper::get_template_part_root_hierarchy();
				$hierarchy = array_merge( $hierarchy, [ get_stylesheet_directory(), get_template_directory() ] );
				$hierarchy = array_unique( $hierarchy );

				foreach ( $hierarchy as $root ) {
					$filename = str_replace( trailingslashit( $root ), '', $filename );
					$filename = preg_replace( '|^/*?([^/]+?)/*?$|', '$1', $filename );
				}

				$filtered_template = apply_filters( 'inc2734_wp_view_controller_controller', $template, $filename );

				return file_exists( $filtered_template ) ? $filtered_template : $template;
			}
		);
	}
);

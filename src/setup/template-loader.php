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
		$types = [
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
		];

		foreach ( $types as $type ) {
			/**
			 * Override query template with hierarchy template.
			 * Only relative paths are stored in $templates.
			 * Since only the files directly under the theme are checked, the extended root is also checked.
			 */
			add_filter(
				"{$type}_template",
				function( $template, $type, $templates ) {
					$located = Helper::locate_template( $templates, false );
					return $located ? $located : $template;
				},
				10,
				3
			);
		}

		add_filter(
			'frontpage_template_hierarchy',
			function( $templates ) {
				$_wp_page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
				if ( $_wp_page_template && 'default' !== $_wp_page_template ) {
					$templates = array_merge( [ $_wp_page_template ], $templates );
				}
				return $templates;
			}
		);

		add_filter(
			'frontpage_template',
			function( $template ) {
				return is_home() ? '' : $template;
			}
		);

		add_filter(
			'home_template_hierarchy',
			function( $templates ) {
				$show_on_front = get_option( 'show_on_front' );
				if ( 'page' !== $show_on_front ) {
					return $templates;
				}

				$page_for_posts    = get_option( 'page_for_posts' );
				$_wp_page_template = get_post_meta( $page_for_posts, '_wp_page_template', true );

				if ( $_wp_page_template && 'default' !== $_wp_page_template ) {
					$templates = array_merge( [ $_wp_page_template ], $templates );
				}
				return $templates;
			}
		);

		add_filter(
			'template_include',
			function( $template ) {
				$filtered_template = apply_filters( 'inc2734_wp_view_controller_controller', $template );
				return file_exists( $filtered_template ) ? $filtered_template : $template;
			}
		);
	}
);

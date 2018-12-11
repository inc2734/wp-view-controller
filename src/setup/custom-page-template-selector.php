<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_View_Controller\Helper;

add_action(
	'after_setup_theme',
	function() {
		foreach ( get_post_types() as $post_type ) {

			/**
			 * Translate page templates on child page
			 *
			 * @param array $post_templates
			 * @param WP_Theme $wp_theme
			 * @param WP_Post $post
			 * @param string $post_type
			 * @return array [{ Relative path from the theme => label }]
			 */
			add_filter(
				"theme_{$post_type}_templates",
				function( $post_templates, $wp_theme, $post, $post_type ) {
					if ( ! is_child_theme() ) {
						return $post_templates;
					}

					foreach ( $post_templates as $base_path => $template_name ) {
						if ( file_exists( get_stylesheet_directory() . '/' . $base_path ) ) {
							return;
						}

						if ( file_exists( get_template_directory() . '/' . $base_path ) ) {
							$page_template_data = get_file_data(
								get_template_directory() . '/' . $base_path,
								[
									'template-name' => 'Template Name',
								]
							);

							$template_name = $page_template_data['template-name'];
							// @codingStandardsIgnoreStart
							$post_templates[ $base_path ] = translate( $template_name, $wp_theme->parent()->get( 'TextDomain' ) );
							// @codingStandardsIgnoreEnd
						}
					}
					return $post_templates;
				},
				10,
				4
			);
		}
	}
);

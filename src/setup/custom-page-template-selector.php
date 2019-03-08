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

					$filterd_post_templates = [];

					foreach ( $post_templates as $base_path => $template_name ) {
						foreach ( [ get_stylesheet_directory(), get_template_directory() ] as $theme_dir_path ) {
							$full_page_tempmlate_path = $theme_dir_path . '/' . $base_path;

							if ( isset( $filterd_post_templates[ $base_path ] ) ) {
								continue;
							}

							if ( ! file_exists( $full_page_tempmlate_path ) ) {
								continue;
							}

							$filterd_post_templates[ $base_path ] = $full_page_tempmlate_path;
						}
					}

					foreach ( $filterd_post_templates as $base_path => $full_page_tempmlate_path ) {
						$page_template_data = get_file_data(
							$full_page_tempmlate_path,
							[
								'template-name' => 'Template Name',
							]
						);

						$template_name = $page_template_data['template-name'];
						// @codingStandardsIgnoreStart
						$post_templates[ $base_path ] = translate( $template_name, $wp_theme->parent()->get( 'TextDomain' ) );
						// @codingStandardsIgnoreEnd
					}

					return $post_templates;
				},
				10,
				4
			);
		}
	}
);

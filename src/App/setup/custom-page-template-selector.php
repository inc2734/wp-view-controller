<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

add_action(
	'after_setup_theme',
	function() {
		foreach ( get_post_types() as $post_type ) {

			/**
			 * Add custom list of page templates
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
					foreach ( wpvc_config( 'page-templates' ) as $page_templates_dir ) {
						foreach ( glob( get_theme_file_path( $page_templates_dir . '/*' ) ) as $page_template_full_path ) {
							$base_template_dirs = wpvc_config( 'templates' );
							foreach ( $base_template_dirs as $base_template_dir ) {
								$page_template = str_replace(
									trailingslashit( get_theme_file_path( '/' . $base_template_dir ) ),
									'',
									$page_template_full_path
								);

								$page_template = str_replace(
									trailingslashit( get_template_directory() ),
									'',
									$page_template
								);

								if ( $page_template !== $page_template_full_path ) {
									break;
								}
							}

							$page_template_data = get_file_data(
								$page_template_full_path,
								[
									'template-name'      => 'Template Name',
									'template-post-type' => 'Template Post Type',
								]
							);

							$template_name = $page_template_data['template-name'];

							$template_post_type = [];
							if ( $page_template_data['template-post-type'] ) {
								$template_post_type = explode( ',', $page_template_data['template-post-type'] );
							}

							if ( ! $template_name ) {
								continue;
							}

							if ( in_array( $template_name, $post_templates ) ) {
								continue;
							}

							if ( ! $template_post_type ) {
								if ( 'page' !== $post_type ) {
									continue;
								}
							} else {
								if ( ! in_array( $post_type, $template_post_type ) ) {
									continue;
								}
							}

							// @codingStandardsIgnoreStart
							if ( ! is_child_theme() ) {
								$post_templates[ $page_template ] = translate( $template_name, wp_get_theme()->get( 'TextDomain' ) );
							} else {
								$post_templates[ $page_template ] = translate( $template_name, wp_get_theme( get_template() )->get( 'TextDomain' ) );
							}
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

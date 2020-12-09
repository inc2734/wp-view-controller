<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_View_Controller\App\Config;
use Inc2734\WP_View_Controller\Helper;

add_action(
	'after_setup_theme',
	function() {
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
			'theme_templates',
			function( $post_templates, $wp_theme, $post, $post_type ) {
				$hierarchy = Helper::get_template_part_root_hierarchy();
				foreach ( $hierarchy as $root ) {
					$page_templates_dirs = Config::get( 'page-templates' );
					foreach ( $page_templates_dirs as $page_templates_dir ) {
						$page_templates_dir_path = trailingslashit( $root ) . $page_templates_dir;
						if ( ! file_exists( $page_templates_dir_path ) ) {
							continue;
						}

						$iterator = new RecursiveDirectoryIterator( $page_templates_dir_path, FilesystemIterator::SKIP_DOTS );
						$iterator = new RecursiveIteratorIterator( $iterator );

						foreach ( $iterator as $file ) {
							if ( ! $file->isFile() ) {
								continue;
							}

							if ( 'php' !== $file->getExtension() ) {
								continue;
							}

							$custom_page_template = realpath( $file->getPathname() );

							$base_path = str_replace( trailingslashit( $root ), '', $custom_page_template );
							if ( ! empty( $post_templates[ $base_path ] ) ) {
								continue;
							}

							$custom_page_template_data = get_file_data(
								$custom_page_template,
								[
									'template-name'      => 'Template Name',
									'template-post-type' => 'Template Post Type',
								]
							);

							if ( ! empty( $custom_page_template_data['template-name'] ) ) {
								$template_post_types = $custom_page_template_data['template-post-type'];
								$template_post_types = $template_post_types ? array_map( 'trim', explode( ',', $template_post_types ) ) : [ 'page' ];
								if ( in_array( $post_type, $template_post_types, true ) ) {
									$post_templates[ $base_path ] = $custom_page_template_data['template-name'];
								}
							}
						}
					}
				}

				if ( ! is_child_theme() ) {
					return $post_templates;
				}

				$filterd_post_templates = [];

				foreach ( $post_templates as $base_path => $template_name ) {
					$located = Helper::locate_template( $base_path, false );
					if ( $located ) {
						$filterd_post_templates[ $base_path ] = $located;
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
);

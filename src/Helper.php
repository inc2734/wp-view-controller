<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller;

use Inc2734\WP_View_Controller\App\Config;
use Inc2734\WP_View_Controller\App\Template_Part;

class Helper {

	/**
	 * Getting config value
	 *
	 * @param string $key the key of the config
	 * @return mixed
	 */
	public static function config( $key = null ) {
		return Config::get( $key );
	}

	/**
	 * Load footer template
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_footer_template( $name = 'footer' ) {
		$slug = static::get_located_template_slug( static::config( 'footer' ), $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug );
	}

	/**
	 * This function like get_footer()
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_footer( $name = null ) {
		do_action( 'get_footer', $name );

		$slug = static::get_located_template_slug( static::config( 'templates' ), 'footer', $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name );
	}

	/**
	 * Load header template
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_header_template( $name = 'header' ) {
		$slug = static::get_located_template_slug( static::config( 'header' ), $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug );
	}

	/**
	 * This function like get_header()
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_header( $name = null ) {
		do_action( 'get_header', $name );

		$slug = static::get_located_template_slug( static::config( 'templates' ), 'header', $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name );
	}

	/**
	 * Load sidebar template
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_sidebar_template( $name = 'sidebar' ) {
		$slug = static::get_located_template_slug( static::config( 'sidebar' ), $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug );
	}

	/**
	 * This function like get_sidebar()
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_sidebar( $name = null ) {
		do_action( 'get_sidebar', $name );

		$slug = static::get_located_template_slug( static::config( 'templates' ), 'sidebar', $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name );
	}

	/**
	 * A template tag that is get_template_part() using variables
	 *
	 * @param string $slug
	 * @param string $name
	 * @param array $vars
	 * @return void
	 */
	public static function get_template_part( $slug, $name = null, array $vars = [] ) {
		/**
		 * @deprecated
		 */
		$args = apply_filters(
			'inc2734_view_controller_get_template_part_args',
			[
				'slug' => $slug,
				'name' => $name,
				'vars' => $vars,
			]
		);

		$args = apply_filters( 'inc2734_wp_view_controller_get_template_part_args', $args);

		/**
		 * @deprecated
		 */
		do_action( 'inc2734_view_controller_get_template_part_pre_render', $args );

		do_action( 'inc2734_wp_view_controller_get_template_part_pre_render', $args );

		$template_part = new Template_Part( $args['slug'], $args['name'] );
		$template_part->set_vars( $args['vars'] );
		$template_part->render();

		/**
		 * @deprecated
		 */
		do_action( 'inc2734_view_controller_get_template_part_post_render', $args );

		do_action( 'inc2734_wp_view_controller_get_template_part_post_render', $args );
	}

	/**
	 * Load wrapper template
	 *
	 * @param string $name
	 * @param array $vars
	 * @return void
	 */
	public static function get_wrapper_template( $name = 'wrapper', array $vars = array() ) {
		$slug = static::get_located_template_slug( static::config( 'layout' ), $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, null, $vars );
	}

	/**
	 * Add template_part_root_hierarchy check to locate_template()
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @see https://developer.wordpress.org/reference/functions/locate_template/
	 *
	 * @param string|array $template_names
	 * @param boolean $load
	 * @param boolean $require_once
	 * @param string $slug Optional
	 * @param string $name Optional
	 * @return string
	 */
	public static function locate_template( $template_names, $load = false, $require_once = true, $slug = null, $name = null ) {
		$cache_key   = hash( 'sha256', json_encode( $template_names ) );
		$cache_group = 'inc2734/wp-view-controller/locate_template';
		$cache       = wp_cache_get( $cache_key, $cache_group );

		if ( false !== $cache && file_exists( $cache ) ) {
			if ( $load ) {
				load_template( $cache, $require_once );
			}
			return $cache;
		}

		foreach ( (array) $template_names as $template_name ) {
			if ( is_null( $slug ) ) {
				$slug = static::filename_to_slug( $template_name );
			}

			$hierarchy = static::get_completed_hierarchy( $slug, $name );

			foreach ( $hierarchy as $root ) {
				$located = trailingslashit( $root ) . $template_name;
				if ( ! file_exists( $located ) ) {
					continue;
				}

				if ( $load && '' != $located ) {
					load_template( $located, $require_once );
				}
				wp_cache_set( $cache_key, $located, $cache_group );
				return $located;
			}
		}

		return '';
	}

	/**
	 * Return template part root hierarchy
	 *
	 * @param string $slug
	 * @param string $name
	 * @param array $vars
	 * @return array
	 */
	public static function get_template_part_root_hierarchy( $slug = null, $name = null, array $vars = [] ) {
		/**
		 * @deprecated
		 */
		$root = apply_filters(
			'inc2734_view_controller_template_part_root',
			'',
			$slug,
			$name,
			$vars
		);

		$hierarchy = [];

		if ( $root ) {
			$hierarchy[] = $root;
		}

		/**
		 * @deprecated
		 */
		$hierarchy = apply_filters(
			'inc2734_view_controller_template_part_root_hierarchy',
			$hierarchy,
			$slug,
			$name,
			$vars
		);

		$hierarchy = apply_filters(
			'inc2734_wp_view_controller_template_part_root_hierarchy',
			$hierarchy,
			$slug,
			$name,
			$vars
		);

		return array_unique( $hierarchy );
	}

	/**
	 * Return located template slug
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @param array $relative_dir_paths
	 * @param string $slug
	 * @param string $name
	 * @return string
	 */
	public static function get_located_template_slug( array $relative_dir_paths, $slug, $name = null ) {
		$slug = static::filename_to_slug( $slug );

		foreach ( $relative_dir_paths as $relative_dir_path ) {
			$maybe_completed_slug = $relative_dir_path ? trailingslashit( $relative_dir_path ) . $slug : $slug;

			$template_names = [];
			if ( ! is_null( $name ) && '' !== $name ) {
				$template_names[] = $maybe_completed_slug . '-' . $name . '.php';
			}
			$template_names[] = $maybe_completed_slug . '.php';

			$located = static::locate_template( $template_names, false );
			if ( $located ) {
				return $maybe_completed_slug;
			}
		}

		/**
		 * @deprecated
		 */
		$fallback_slug = apply_filters(
			'inc2734_view_controller_located_template_slug_fallback',
			null,
			$relative_dir_paths,
			$slug,
			$name
		);

		$fallback_slug = apply_filters(
			'inc2734_wp_view_controller_located_template_slug_fallback',
			$fallback_slug,
			$relative_dir_paths,
			$slug,
			$name
		);

		if ( $fallback_slug ) {
			return $fallback_slug;
		}

		return false;
	}

	/**
	 * Return slug from filename
	 *
	 * @param string $filename
	 * @return string
	 */
	public static function filename_to_slug( $filename ) {
		$filename = trim( $filename );
		$filename = trim( $filename, '/' );
		return preg_replace( '|\.php$|', '', $filename );
	}

	/**
	 * Returns array of layout templates
	 *
	 * @return array
	 */
	public static function get_wrapper_templates() {
		return static::_get_candidate_locate_templates( static::config( 'layout' ) );
	}

	/**
	 * Returns array of header templates
	 *
	 * @return array
	 */
	public static function get_header_templates() {
		return static::_get_candidate_locate_templates( static::config( 'header' ) );
	}

	/**
	 * Returns candidate locate templates
	 *
	 * @return array
	 */
	protected static function _get_candidate_locate_templates( array $relative_dir_paths ) {
		$hierarchy = static::get_completed_hierarchy();

		$completed_hierarchy = [];
		foreach ( $hierarchy as $root ) {
			foreach ( $relative_dir_paths as $relative_dir_path ) {
				$completed_hierarchy[] = $root . '/' . $relative_dir_path;
			}
		}

		$wp_theme = wp_get_theme();
		$text_domain = is_child_theme() ? $wp_theme->parent()->get( 'TextDomain' ) : $wp_theme->get( 'TextDomain' );

		$templates = [];
		foreach ( $completed_hierarchy as $wrapper_dir ) {
			foreach ( glob( $wrapper_dir . '/*.php' ) as $file ) {
				$slug = static::filename_to_slug( str_replace( $wrapper_dir . '/', '', $file ) );
				$name = trim( preg_match( '|Name:(.*)$|mi', file_get_contents( $file ), $header ) ? $header[1] : $slug );
				// @codingStandardsIgnoreStart
				$templates[ $slug ] = translate( $name, $text_domain );
				// @codingStandardsIgnoreEnd
			}
		}

		return $templates;
	}

	/**
	 * Return hierarchy + stylesheet_directory + template_directory
	 *
	 * @param string $slug
	 * @param string $name
	 * @return array
	 */
	public static function get_completed_hierarchy( $slug = null, $name = null ) {
		$hierarchy = static::get_template_part_root_hierarchy( $slug, $name );
		$hierarchy = array_merge( $hierarchy, [ get_stylesheet_directory(), get_template_directory() ] );
		$hierarchy = array_unique( $hierarchy );
		return $hierarchy;
	}
}

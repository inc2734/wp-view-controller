<?php
/**
 * @package wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\App\Contract;

use Inc2734\WP_View_Controller\App\Config;
use Inc2734\WP_View_Controller\App\Template_Part;

trait Template_Tag {

	/**
	 * Load footer template.
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $args Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_footer_template( $slug = 'footer', $name = null, array $args = [] ) {
		$slug = static::get_located_template_slug( Config::get( 'footer' ), $slug );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name, $args );
	}

	/**
	 * This function like get_footer().
	 *
	 * @param string $name The name of the specialised template.
	 * @param array  $args Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_footer( $name = null, array $args = [] ) {
		do_action( 'get_footer', $name );

		$slug = static::get_located_template_slug( Config::get( 'templates' ), 'footer', $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name, $args );
	}

	/**
	 * Load header template.
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $args Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_header_template( $slug = 'header', $name = null, array $args = [] ) {
		$slug = static::get_located_template_slug( Config::get( 'header' ), $slug );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name, $args );
	}

	/**
	 * This function like get_header().
	 *
	 * @param string $name The name of the specialised template.
	 * @param array  $args Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_header( $name = null, array $args = [] ) {
		do_action( 'get_header', $name );

		$slug = static::get_located_template_slug( Config::get( 'templates' ), 'header', $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name, $args );
	}

	/**
	 * Load sidebar template.
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $args Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_sidebar_template( $slug = 'sidebar', $name = null, array $args = [] ) {
		$slug = static::get_located_template_slug( Config::get( 'sidebar' ), $slug );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name, $args );
	}

	/**
	 * This function like get_sidebar().
	 *
	 * @param string $name The name of the specialised template.
	 * @param array  $args Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_sidebar( $name = null, array $args = [] ) {
		do_action( 'get_sidebar', $name );

		$slug = static::get_located_template_slug( Config::get( 'templates' ), 'sidebar', $name );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, $name, $args );
	}

	/**
	 * Load wrapper template.
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param array  $vars Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_wrapper_template( $slug = 'wrapper', array $vars = [] ) {
		$slug = static::get_located_template_slug( Config::get( 'layout' ), $slug );

		if ( ! $slug ) {
			return;
		}

		static::get_template_part( $slug, null, $vars );
	}

	/**
	 * Return located template slug
	 *
	 * @param array  $relative_dir_paths Relative papth of target directories.
	 * @param string $slug               The slug name for the generic template.
	 * @param string $name               The name of the specialised template.
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

			$located = static::locate_template( $template_names, false, false, $slug, $name );
			if ( $located ) {
				return $maybe_completed_slug;
			}
		}

		$fallback_slug = apply_filters(
			'inc2734_wp_view_controller_located_template_slug_fallback',
			null,
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
	 * Return slug from filename.
	 *
	 * @param string $filename The file name.
	 * @return string
	 */
	public static function filename_to_slug( $filename ) {
		$filename = trim( $filename );
		$filename = trim( $filename, '/' );
		return preg_replace( '|\.php$|', '', $filename );
	}

	/**
	 * Add template_part_root_hierarchy check to locate_template().
	 *
	 * @see https://developer.wordpress.org/reference/functions/locate_template/
	 *
	 * @param string|array $template_names Template file(s) to search for, in order.
	 * @param boolean      $load           If true the template file will be loaded if it is found.
	 * @param boolean      $require_once   Whether to require_once or require.
	 * @param string       $slug           The slug name for the generic template.
	 * @param string       $name           The name of the specialised template.
	 * @param array        $args           Additional arguments passed to the template.
	 * @return string
	 */
	public static function locate_template( $template_names, $load = false, $require_once = true, $slug = null, $name = null, array $args = [] ) {
		$cache_key   = md5( json_encode( $template_names ) );
		$cache_group = 'inc2734/wp-view-controller/locate_template';
		$cache       = wp_cache_get( $cache_key, $cache_group );

		if ( false !== $cache && file_exists( $cache ) ) {
			if ( $load ) {
				load_template( $cache, $require_once, $args );
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

				if ( $load && $located ) {
					load_template( $located, $require_once, $args );
				}
				wp_cache_set( $cache_key, $located, $cache_group );
				return $located;
			}
		}

		return '';
	}

	/**
	 * A template tag that is get_template_part() using variables
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $vars Additional arguments passed to the template.
	 * @return void
	 */
	public static function get_template_part( $slug, $name = null, array $vars = [] ) {
		$args = [
			'slug' => $slug,
			'name' => $name,
			'vars' => $vars,
		];

		if ( array_key_exists( '_context', $args['vars'] ) ) {
			$context = $args['vars']['_context'];
		}

		$args = apply_filters( 'inc2734_wp_view_controller_get_template_part_args', $args );
		if ( isset( $args['vars']['_context'] ) ) {
			unset( $args['vars']['_context'] );
		}

		if ( isset( $context ) ) {
			$args['vars']['_context'] = $context;
		}

		do_action( 'inc2734_wp_view_controller_get_template_part_pre_render', $args );

		$template_part = new Template_Part( $args['slug'], $args['name'], $args['vars'] );
		$template_part->render();

		do_action( 'inc2734_wp_view_controller_get_template_part_post_render', $args );
	}

	/**
	 * Return hierarchy + stylesheet_directory + template_directory
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @return array
	 */
	public static function get_completed_hierarchy( $slug = null, $name = null ) {
		$hierarchy = static::get_template_part_root_hierarchy( $slug, $name );
		$hierarchy = array_merge( $hierarchy, [ get_stylesheet_directory(), get_template_directory() ] );
		$hierarchy = array_unique( $hierarchy );
		return $hierarchy;
	}

	/**
	 * Return template part root hierarchy
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $vars Additional arguments passed to the template.
	 * @return array
	 */
	public static function get_template_part_root_hierarchy( $slug = null, $name = null, array $vars = [] ) {
		$hierarchy = [];

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
	 * Returns array of layout templates.
	 *
	 * @return array
	 */
	public static function get_wrapper_templates() {
		return static::_get_candidate_locate_templates( Config::get( 'layout' ) );
	}

	/**
	 * Returns array of header templates.
	 *
	 * @return array
	 */
	public static function get_header_templates() {
		return static::_get_candidate_locate_templates( Config::get( 'header' ) );
	}

	/**
	 * Getting config value.
	 *
	 * @param string $key The key of the config.
	 * @return mixed
	 */
	protected static function config( $key = null ) {
		return Config::get( $key );
	}

	/**
	 * Returns candidate locate templates.
	 *
	 * @param array $relative_dir_paths Relative path of target directories.
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

		$wp_theme    = wp_get_theme();
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
}

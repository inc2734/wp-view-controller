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
		$template_name = static::locate_template( (array) static::config( 'footer' ), $name );

		if ( empty( $template_name ) ) {
			return;
		}

		static::get_template_part( $template_name );
	}


	/**
	 * This function like get_footer()
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_footer( $name = null ) {
		do_action( 'get_footer', $name );

		if ( '' !== $name && file_exists( get_theme_file_path( "footer-{$name}.php" ) ) ) {
			\locate_template( "footer-{$name}.php", true, false );
			return;
		}
		if ( file_exists( get_theme_file_path( 'footer.php' ) ) ) {
			\locate_template( 'footer.php', true, false );
			return;
		}

		$template_name = static::locate_template( (array) static::config( 'templates' ), 'footer', $name );
		if ( empty( $template_name ) ) {
			return;
		}

		static::get_template_part( $template_name );
	}

	/**
	 * Load header template
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_header_template( $name = 'header' ) {
		$template_name = static::locate_template( (array) static::config( 'header' ), $name );

		if ( empty( $template_name ) ) {
			return;
		}

		static::get_template_part( $template_name );
	}

	/**
	 * This function like get_header()
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_header( $name = null ) {
		do_action( 'get_header', $name );

		if ( '' !== $name && file_exists( get_theme_file_path( "header-{$name}.php" ) ) ) {
			\locate_template( "header-{$name}.php", true, false );
			return;
		}
		if ( file_exists( get_theme_file_path( 'header.php' ) ) ) {
			\locate_template( 'header.php', true, false );
			return;
		}

		$template_name = static::locate_template( (array) static::config( 'templates' ), 'header', $name );
		if ( empty( $template_name ) ) {
			return;
		}

		static::get_template_part( $template_name );
	}

	/**
	 * Load sidebar template
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_sidebar_template( $name = 'sidebar' ) {
		$template_name = static::locate_template( (array) static::config( 'sidebar' ), $name );

		if ( empty( $template_name ) ) {
			return;
		}

		static::get_template_part( $template_name );
	}

	/**
	 * This function like get_sidebar()
	 *
	 * @param string $name
	 * @return void
	 */
	public static function get_sidebar( $name = null ) {
		do_action( 'get_sidebar', $name );

		if ( '' !== $name && file_exists( get_theme_file_path( "sidebar-{$name}.php" ) ) ) {
			\locate_template( "sidebar-{$name}.php", true, false );
			return;
		}
		if ( file_exists( get_theme_file_path( 'sidebar.php' ) ) ) {
			\locate_template( 'sidebar.php', true, false );
			return;
		}

		$template_name = static::locate_template( (array) static::config( 'templates' ), 'sidebar', $name );
		if ( empty( $template_name ) ) {
			return;
		}

		static::get_template_part( $template_name );
	}

	/**
	 * Return template name by $slug and $name that based in theme directory
	 *
	 * @param string $slug
	 * @param string $name
	 * @return null|string Template name that can be used in get_template_part()
	 */
	public static function get_template_name( $slug, $name ) {
		$template_names = [];
		if ( $name ) {
			$template_names[] = $slug . '-' . $name . '.php';
		}
		$template_names[] = $slug . '.php';

		$template_path = \locate_template( $template_names, false );
		$template_name = '';
		if ( $template_path ) {
			if ( false !== strpos( $template_path, $slug . '-' . $name . '.php' ) ) {
				$template_name = $slug . '-' . $name;
			} else {
				$template_name = $slug;
			}
		}

		return $template_name;
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
		$args = apply_filters(
			'inc2734_view_controller_get_template_part_args',
			[
				'slug' => $slug,
				'name' => $name,
				'vars' => $vars,
			]
		);

		do_action( 'inc2734_view_controller_get_template_part_pre_render', $args );

		if ( false !== has_action( 'inc2734_view_controller_get_template_part_' . $args['slug'] ) ) {
			do_action( 'inc2734_view_controller_get_template_part_' . $args['slug'], $args['name'], $args['vars'] );
			return;
		}

		if ( $name ) {
			if ( false !== has_action( 'inc2734_view_controller_get_template_part_' . $args['slug'] . '-' . $args['name'] ) ) {
				do_action( 'inc2734_view_controller_get_template_part_' . $args['slug'] . '-' . $args['name'], $args['vars'] );
				return;
			}
		}

		$template_part = new Template_Part( $args['slug'], $args['name'] );
		$template_part->set_vars( $args['vars'] );
		$template_part->render();

		do_action( 'inc2734_view_controller_get_template_part_post_render', $args );
	}

	/**
	 * Load wrapper template
	 *
	 * @param string $name
	 * @param array $args
	 * @return void
	 */
	public static function get_wrapper_template( $name = 'wrapper', array $args = array() ) {
		$template_name = static::locate_template( (array) static::config( 'layout' ), $name );

		if ( empty( $template_name ) ) {
			return;
		}

		static::get_template_part( $template_name, null, $args );
	}

	/**
	 * Locate template that based in theme directory
	 *
	 * @param array $directory_slugs Template name that can be used in get_template_part()
	 * @param string $slug
	 * @param string $name
	 * @return null|string Template name that can be used in get_template_part()
	 */
	public static function locate_template( $directory_slugs, $slug, $name = '' ) {
		$directory_slugs = (array) $directory_slugs;
		$slug = preg_replace( '|\.php$|', '', $slug );

		if ( empty( $directory_slugs ) ) {
			return static::get_template_name( $slug, $name );
		}

		foreach ( $directory_slugs as $directory_slug ) {
			if ( $directory_slug ) {
				$new_slug = $directory_slug . '/' . $slug;
			} else {
				$new_slug = $slug;
			}

			$template_name = static::get_template_name( $new_slug, $name );
			if ( $template_name ) {
				return $template_name;
			}
		}
	}
}

<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\App;

use Inc2734\WP_View_Controller\Helper;

class Template_Part {

	/**
	 * Rendering the template part.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_template_part/
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $vars Additional arguments passed to the template.
	 */
	public static function render( $slug, $name = null, $vars = [] ) {
		do_action( "get_template_part_{$slug}", $slug, $name, $vars );

		$locate_template = null;

		$templates = [];
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		do_action( 'get_template_part', $slug, $name, $templates, $vars );

		$html = apply_filters(
			'inc2734_wp_view_controller_pre_template_part_render',
			null,
			$slug,
			$name,
			$vars
		);

		if ( is_null( $html ) ) {
			$action_with_name = "inc2734_wp_view_controller_get_template_part_{$slug}-{$name}";
			$action           = "inc2734_wp_view_controller_get_template_part_{$slug}";

			if ( $name && has_action( $action_with_name ) ) {
				ob_start();
				// @deprecated
				do_action( $action_with_name, $vars );
				$html = ob_get_clean();
			} elseif ( has_action( $action ) ) {
				ob_start();
				// @deprecated
				do_action( $action, $name, $vars );
				$html = ob_get_clean();
			}
		}

		do_action( 'inc2734_wp_view_controller_get_template_part', $slug, $name, $templates, $html, $vars );

		if ( is_null( $html ) ) {
			static::_init_template_args( $vars );

			ob_start();
			$locate_template = Helper::locate_template( $templates, true, false, $slug, $name, $vars );
			$html            = ob_get_clean();

			static::_reset_template_args();
		}

		if ( $html && static::_enable_debug_mode() ) {
			static::_debug_comment( $slug, $name, 'Start : ', $locate_template );
		}

		$html = apply_filters(
			'inc2734_wp_view_controller_template_part_render',
			$html,
			$slug,
			$name,
			$vars
		);

		echo $html; // xss ok.

		if ( $html && static::_enable_debug_mode() ) {
			static::_debug_comment( $slug, $name, 'End : ', $locate_template );
		}
	}

	/**
	 * Initialize template args.
	 *
	 * @param array $vars Additional arguments passed to the template.
	 */
	protected static function _init_template_args( $vars ) {
		global $wp_version, $wp_query;

		set_query_var( '_wp_view_controller_backup_query_vars', $wp_query->query_vars );

		if ( version_compare( $wp_version, '5.5' ) < 0 ) {
			$vars['args'] = $vars;
		}

		foreach ( $vars as $var => $value ) {
			if ( null === get_query_var( $var, null ) ) {
				set_query_var( $var, $value );
			}
		}
	}

	/**
	 * Reset template args.
	 */
	protected static function _reset_template_args() {
		global $wp_query;

		$backup_query_vars    = get_query_var( '_wp_view_controller_backup_query_vars' );
		$backup_query_vars    = is_array( $backup_query_vars ) ? $backup_query_vars : [];
		$wp_query->query_vars = $backup_query_vars;
	}

	/**
	 * Return true when enable debug mode.
	 *
	 * @return boolean
	 */
	protected static function _enable_debug_mode() {
		if ( ! Helper::is_debug_mode() ) {
			return;
		}

		if ( is_customize_preview() || is_admin() ) {
			return;
		}

		if ( function_exists( 'tests_add_filter' ) ) {
			return;
		}

		return true;
	}

	/**
	 * Print debug comment.
	 *
	 * @param string $slug            The slug name for the generic template.
	 * @param string $name            The name of the specialised template.
	 * @param string $prefix          Prefix message.
	 * @param string $locate_template Result of Helper::locate_template().
	 * @return void
	 */
	public static function _debug_comment( $slug, $name, $prefix = null, $locate_template = null ) {
		$template_slug = null;

		if ( $locate_template ) {
			$hierarchy = Helper::get_completed_hierarchy( $slug, $name );
			foreach ( $hierarchy as $root ) {
				if ( 0 === strpos( $locate_template, $root ) ) {
					$template_slug = Helper::filename_to_slug( str_replace( $root, '', $locate_template ) );
					break;
				}
			}
		}

		printf(
			"\n" . '<!-- %1$s[slug] => %2$s [name] => %3$s [template-slug] => %4$s -->' . "\n",
			esc_html( $prefix ),
			esc_html( $slug ),
			esc_html( $name ),
			esc_html( $template_slug )
		);
	}
}

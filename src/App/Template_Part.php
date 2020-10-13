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
	 * The template path like get_template_part().
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The name like get_template_part().
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Variables.
	 *
	 * @var array
	 */
	protected $vars = [];

	/**
	 * Constructor.

	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $vars Additional arguments passed to the template.
	 * @return void
	 */
	public function __construct( $slug, $name = null, $vars = [] ) {
		$this->slug = $slug;
		$this->name = $name;
		$this->vars = $vars;
	}

	/**
	 * Rendering the template part.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_template_part/
	 *
	 * @return void
	 */
	public function render() {
		do_action( "get_template_part_{$this->slug}", $this->slug, $this->name, $this->vars );

		$locate_template = null;

		$templates = [];
		$name      = (string) $this->name;
		if ( '' !== $name ) {
			$templates[] = "{$this->slug}-{$name}.php";
		}

		$templates[] = "{$this->slug}.php";

		do_action( 'get_template_part', $this->slug, $name, $templates, $this->vars );

		$html = apply_filters(
			'inc2734_wp_view_controller_pre_template_part_render',
			null,
			$this->slug,
			$this->name,
			$this->vars
		);

		if ( is_null( $html ) ) {
			$action_with_name = "inc2734_wp_view_controller_get_template_part_{$this->slug}-{$this->name}";
			$action           = "inc2734_wp_view_controller_get_template_part_{$this->slug}";

			if ( $this->name && has_action( $action_with_name ) ) {
				ob_start();
				// @deprecated
				do_action( $action_with_name, $this->vars );
				$html = ob_get_clean();
			} elseif ( has_action( $action ) ) {
				ob_start();
				// @deprecated
				do_action( $action, $this->name, $this->vars );
				$html = ob_get_clean();
			}
		}

		do_action( 'inc2734_wp_view_controller_get_template_part', $this->slug, $this->name, $templates, $html, $this->vars );

		if ( is_null( $html ) ) {
			$this->_init_template_args();

			ob_start();
			$locate_template = Helper::locate_template( $templates, true, false, $this->slug, $this->name, $this->vars );
			$html            = ob_get_clean();

			$this->_reset_template_args();
		}

		if ( $html && $this->_enable_debug_mode() ) {
			$this->_debug_comment( 'Start : ', $locate_template );
		}

		$html = apply_filters(
			'inc2734_wp_view_controller_template_part_render',
			$html,
			$this->slug,
			$this->name,
			$this->vars
		);

		echo $html; // xss ok.

		if ( $html && $this->_enable_debug_mode() ) {
			$this->_debug_comment( 'End : ', $locate_template );
		}
	}

	/**
	 * Initialize template args.
	 */
	protected function _init_template_args() {
		global $wp_version, $wp_query;

		set_query_var( '_wp_view_controller_backup_query_vars', $wp_query->query_vars );

		if ( version_compare( $wp_version, '5.5' ) < 0 ) {
			$this->vars['args'] = $this->vars;
		}

		foreach ( $this->vars as $var => $value ) {
			if ( null === get_query_var( $var, null ) ) {
				set_query_var( $var, $value );
			}
		}
	}

	/**
	 * Reset template args.
	 */
	protected function _reset_template_args() {
		global $wp_query;

		$wp_query->query_vars = get_query_var( '_wp_view_controller_backup_query_vars' );
	}

	/**
	 * Return true when enable debug mode.
	 *
	 * @return boolean
	 */
	protected function _enable_debug_mode() {
		if ( ! apply_filters( 'inc2734_wp_view_controller_debug', true ) ) {
			return;
		}

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
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
	 * @param string $prefix          Prefix message.
	 * @param string $locate_template Result of Helper::locate_template().
	 * @return void
	 */
	public function _debug_comment( $prefix = null, $locate_template = null ) {
		$template_slug = null;

		if ( $locate_template ) {
			$hierarchy = Helper::get_completed_hierarchy( $this->slug, $this->name );
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
			esc_html( $this->slug ),
			esc_html( $this->name ),
			esc_html( $template_slug )
		);
	}
}

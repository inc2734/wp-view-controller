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
	 * The template path like get_template_part()
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The name like get_template_part()
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Variables
	 *
	 * @var array
	 */
	protected $vars = [];

	/**
	 * Variables
	 *
	 * @var array
	 */
	protected $keys_to_wp_query = [];

	/**
	 * WP_Query object
	 *
	 * @var WP_Query
	 */
	protected $wp_query;

	/**
	 * @param string $slug
	 * @param string $name
	 * @return void
	 */
	public function __construct( $slug, $name = null ) {
		global $wp_query;

		$this->slug     = $slug;
		$this->name     = $name;
		$this->wp_query = $wp_query;
	}

	/**
	 * Sets a variable
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set_var( $key, $value ) {
		if ( null === $this->wp_query->get( $key, null ) ) {
			$this->wp_query->set( $key, $value );
			$this->keys_to_wp_query[ $key ] = $value;
		}
		$this->vars[ $key ] = $value;
	}

	/**
	 * Sets variables
	 *
	 * @param array $vars
	 * @return void
	 */
	public function set_vars( array $vars = [] ) {
		foreach ( $vars as $key => $value ) {
			$this->set_var( $key, $value );
		}
	}

	/**
	 * Rendering the template part
	 *
	 * @return void
	 */
	public function render() {
		do_action( "get_template_part_{$this->slug}", $this->slug, $this->name );

		$template_names  = $this->_generate_template_names();
		$locate_template = null;

		do_action( 'get_template_part', $this->slug, $this->name, $template_names );

		$html = apply_filters(
			'inc2734_wp_view_controller_pre_template_part_render',
			null,
			$this->slug,
			$this->name,
			$this->vars
		);

		if ( is_null( $html ) ) {
			$action_with_name = 'inc2734_wp_view_controller_get_template_part_' . $this->slug . '-' . $this->name;
			$action           = 'inc2734_wp_view_controller_get_template_part_' . $this->slug;
			if ( $this->name && has_action( $action_with_name ) ) {
				ob_start();
				do_action( $action_with_name, $this->vars );
				$html = ob_get_clean();
			} elseif ( has_action( $action ) ) {
				ob_start();
				do_action( $action, $this->name, $this->vars );
				$html = ob_get_clean();
			}
		}

		do_action( 'inc2734_wp_view_controller_get_template_part', $this->slug, $this->name, $template_names, $html );

		if ( is_null( $html ) ) {
			ob_start();
			Helper::locate_template( $template_names, true, false, $this->slug, $this->name );
			$locate_template = Helper::locate_template( $template_names, false, false, $this->slug, $this->name );
			$html = ob_get_clean();
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

		foreach ( $this->keys_to_wp_query as $key => $value ) {
			unset( $value );
			$this->wp_query->set( $key, null );
		}
	}

	protected function _generate_template_names() {
		$template_names = [];

		if ( $this->name ) {
			$template_names[] = $this->slug . '-' . $this->name . '.php';
		}
		$template_names[] = $this->slug . '.php';

		return $template_names;
	}

	/**
	 * Return true when enable debug mode
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
	 * Print debug comment
	 *
	 * @param string $prefix
	 * @param string $locate_template
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

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
			$this->vars[ $key ] = $value;
			$this->wp_query->set( $key, $value );
		}
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
		$is_root = false;
		$html = '';

		$template_part_root = apply_filters( 'inc2734_view_controller_template_part_root', '', $this->slug, $this->name, $this->vars );
		if ( $template_part_root ) {
			ob_start();
			$is_root = $this->_root_get_template_part( $template_part_root ) ? true : false;
			$html = ob_get_clean();
		}

		if ( ! $is_root || ! $template_part_root ) {
			ob_start();
			get_template_part( $this->slug, $this->name );
			$html = ob_get_clean();
		}

		// @codingStandardsIgnoreStart
		echo apply_filters( 'inc2734_view_controller_template_part_render', $html, $this->slug, $this->name, $this->vars );
		// @codingStandardsIgnoreEnd

		foreach ( $this->vars as $key => $value ) {
			unset( $value );
			$this->wp_query->set( $key, null );
		}
	}

	/**
	 * @see https://developer.wordpress.org/reference/functions/get_template_part/
	 */
	protected function _root_get_template_part( $template_part_root ) {
		do_action( 'get_template_part_' . $this->slug, $this->slug, $this->name );

		$template_part_root = trailingslashit( $template_part_root );
		if ( $this->name ) {
			$templates[] = $template_part_root . $this->slug . '-' . $this->name . '.php';
		}
		$templates[] = $template_part_root . $this->slug . '.php';

		return $this->_root_locate_template( $templates );
	}

	/**
	 * @see https://developer.wordpress.org/reference/functions/locate_template/
	 */
	protected function _root_locate_template( $templates ) {
		$located = '';

		foreach ( (array) $templates as $template ) {
			if ( ! $template ) {
				continue;
			} elseif ( file_exists( $template ) ) {
				$located = $template;
				break;
			}
		}

		if ( $located ) {
			load_template( $located, true, false );
		}

		return $located;
	}
}

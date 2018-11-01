<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\App;

class Template_Part {

	/**
	 * The template path like get_template_part()
	 *
	 * @var string
	 */
	protected $template;

	/**
	 * The slug like get_template_part()
	 *
	 * @var string
	 */
	protected $slug;

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
	 * @param string $template
	 * @param string $slug
	 * @return void
	 */
	public function __construct( $template, $slug = null ) {
		global $wp_query;

		$this->template = $template;
		$this->slug     = $slug;
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
		if ( null === $this->wp_query->get( $key, null ) || ! isset( $this->vars[ $key ] ) ) {
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
	public function set_vars( $vars ) {
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
		ob_start();
		get_template_part( $this->template, $this->slug );

		// @codingStandardsIgnoreStart
		echo apply_filters( 'inc2734_view_controller_template_part_render', ob_get_clean(), $this->template, $this->slug, $this->vars );
		// @codingStandardsIgnoreEnd

		foreach ( $this->vars as $key => $value ) {
			unset( $value );
			$this->wp_query->set( $key, null );
		}
	}
}

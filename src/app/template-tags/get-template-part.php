<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * This class make function that like get_template_part().
 * In addition to calling templates, you can pass variables.
 */
class Inc2734_WP_View_Controller_Template_Part {

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
	public function __construct( $template, $slug = '' ) {
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
		get_template_part( $this->template, $this->slug );
		foreach ( $this->vars as $key => $value ) {
			unset( $value );
			$this->wp_query->set( $key, null );
		}
	}
}

/**
 * A template tag that is get_template_part() using variables
 *
 * @param string $template
 * @param array $vars
 * @return void
 */
function wpvc_get_template_part( $template, $vars = [] ) {
	$template_part = new Inc2734_WP_View_Controller_Template_Part( $template );
	$template_part->set_vars( $vars );
	$template_part->render();
}

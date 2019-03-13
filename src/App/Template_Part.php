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
	 * The template part root (Default is '' = theme directory)
	 *
	 * @var string
	 */
	protected $root;

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
		$html = '';

		ob_start();
		if ( $this->_is_root_template() ) {
			$this->_root_get_template_part();
		} else {
			get_template_part( $this->slug, $this->name );
		}
		$html = ob_get_clean();

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
	protected function _root_get_template_part() {
		do_action( 'get_template_part_' . $this->slug, $this->slug, $this->name );
		return $this->_root_locate_template( true );
	}

	/**
	 * @see https://developer.wordpress.org/reference/functions/locate_template/
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	protected function _root_locate_template( $load = false ) {
		$located = '';

		$templates = $this->_get_root_template_part_slugs();
		foreach ( (array) $templates as $template ) {
			if ( ! $template ) {
				continue;
			} elseif ( file_exists( $template ) ) {
				$located = $template;
				break;
			}
		}

		if ( $load && '' != $located ) {
			load_template( $located, false );
		}

		return $located;
	}

	/**
	 * Return true when the template exists in each roots
	 *
	 * @return boolean
	 */
	protected function _is_root_template() {
		$hierarchy = [];

		/**
		 * @deprecated
		 */
		$root = apply_filters(
			'inc2734_view_controller_template_part_root',
			'',
			$this->slug,
			$this->name,
			$this->vars
		);

		if ( $root ) {
			$hierarchy[] = $root;
		}

		$hierarchy = apply_filters(
			'inc2734_view_controller_template_part_root_hierarchy',
			$hierarchy,
			$this->slug,
			$this->name,
			$this->vars
		);
		$hierarchy = array_unique( $hierarchy );

		foreach ( $hierarchy as $root ) {
			$is_root = $this->_is_root( $root );
			if ( $is_root ) {
				return $is_root;
			}
		}

		return false;
	}

	/**
	 * Return true when the template exists in the root
	 *
	 * @param string $root root directory of template parts
	 * @return boolean
	 */
	protected function _is_root( $root ) {
		$this->root = $root;

		$is_root = (bool) $this->_root_locate_template();
		if ( ! $is_root ) {
			$this->root = '';
		}

		return $is_root;
	}

	/**
	 * Return candidate file names of the root template part
	 *
	 * @return array
	 */
	protected function _get_root_template_part_slugs() {
		if ( ! $this->root ) {
			return [];
		}

		if ( $this->name ) {
			$templates[] = trailingslashit( $this->root ) . $this->slug . '-' . $this->name . '.php';
		}
		$templates[] = trailingslashit( $this->root ) . $this->slug . '.php';

		return $templates;
	}
}

<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\App;

use Inc2734\WP_View_Controller\App\Config;
use Inc2734\WP_View_Controller\Helper;

class View {

	/**
	 * The layout template path.
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * The view template path.
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * The view template name suffix.
	 *
	 * @var string
	 */
	protected $view_suffix;

	/**
	 * Sets the layout template.
	 *
	 * @param string $layout The layout template path.
	 * @return void
	 */
	public function layout( $layout ) {
		$this->layout = $layout;
	}

	/**
	 * Rendering the page.
	 *
	 * @param string $view        The view template path.
	 * @param string $view_suffix The view template suffix.
	 * @return void
	 */
	public function render( $view, $view_suffix = '' ) {
		$this->view        = $view;
		$this->view_suffix = $view_suffix;

		$render_type = is_singular() ? 'loop' : 'direct';
		$render_type = apply_filters( 'inc2734_wp_view_controller_render_type', $render_type );
		switch ( $render_type ) {
			case 'loop':
				$this->_render_loop();
				break;
			case 'direct':
				$this->_render_direct();
				break;
			default:
				break;
		}
	}

	/**
	 * Rendering in the WordPress loop.
	 *
	 * @return void
	 */
	protected function _render_loop() {
		while ( have_posts() ) {
			the_post();
			$this->_render();
		}
	}

	/**
	 * Rendering not in the WordPress loop.
	 *
	 * @return void
	 */
	protected function _render_direct() {
		global $post;
		setup_postdata( $post );
		$this->_render();
	}

	/**
	 * Rendering the layout template.
	 *
	 * @return void
	 */
	protected function _render() {
		$layout = apply_filters( 'inc2734_wp_view_controller_layout', $this->layout );

		Helper::get_wrapper_template(
			$layout,
			[
				'_view_controller' => $this,
			]
		);
	}

	/**
	 * Return layout arg.
	 *
	 * @return string
	 */
	public function get_layout() {
		return $this->layout;
	}

	/**
	 * Return view arg.
	 *
	 * @return string
	 */
	public function get_view() {
		return $this->view;
	}

	/**
	 * Return view_suffix arg.
	 *
	 * @return string
	 */
	public function get_view_suffix() {
		return $this->view_suffix;
	}

	/**
	 * Loading the view template in layout template.
	 *
	 * @return void
	 */
	public function view() {
		$view = $this->_get_args_for_template_part();
		$view = apply_filters( 'inc2734_wp_view_controller_view', $view );
		Helper::get_template_part( $view['slug'], $view['name'] );
	}

	/**
	 * Gets the view args.
	 *
	 * @return array
	 */
	protected function _get_args_for_template_part() {
		$view = [
			'slug' => '',
			'name' => '',
		];

		$slug = Helper::get_located_template_slug( Config::get( 'view' ), $this->view, $this->view_suffix );

		if ( ! $slug ) {
			return $view;
		}

		$view = [
			'slug' => $slug,
			'name' => $this->view_suffix,
		];

		if ( is_404() || is_search() ) {
			return $view;
		}

		$static_template_name = $this->get_static_view_template_name();
		if ( $static_template_name ) {
			return [
				'slug' => $static_template_name,
				'name' => '',
			];
		}

		return $view;
	}

	/**
	 * Returns static view template name.
	 *
	 * @return string|null
	 */
	public function get_static_view_template_name() {
		$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$request_uri = $this->_get_relative_path( $request_uri );
		$path        = $this->_remove_http_query( $request_uri );
		$path        = $this->_remove_paged_slug( $path );
		$path        = trim( $path, '/' );

		if ( ! $path ) {
			return Helper::get_located_template_slug( Config::get( 'static' ), 'index' );
		}

		$slug = Helper::get_located_template_slug( Config::get( 'static' ), $path );
		if ( $slug ) {
			return $slug;
		}

		$slug = Helper::get_located_template_slug( Config::get( 'static' ), $path . '/index' );
		if ( $slug ) {
			return $slug;
		}
	}

	/**
	 * Return relative path from $uri.
	 *
	 * @param string $uri The URI.
	 * @return string
	 */
	protected function _get_relative_path( $uri ) {
		return str_replace( home_url(), '', $uri );
	}

	/**
	 * Return uri that removed http queries.
	 *
	 * @param string $uri The URI.
	 * @return string
	 */
	protected function _remove_http_query( $uri ) {
		$uri = str_replace( http_build_query( $_GET, null, '&' ), '', $uri );
		$uri = rtrim( $uri, '?' );
		return $uri;
	}

	/**
	 * Return uri that removed /page/xx/ and /paged/xx/.
	 *
	 * @param string $uri The URI.
	 * @return string
	 */
	protected function _remove_paged_slug( $uri ) {
		if ( ! is_paged() ) {
			return $uri;
		}
		return preg_replace( '/^(.+?)\/page\/\d+/', '$1', $uri );
	}
}

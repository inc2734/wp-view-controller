<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * View class
 */
class Inc2734_WP_View_Controller_View {

	/**
	 * The layout template path
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * The view template path
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * The view template name suffix
	 *
	 * @var string
	 */
	protected $view_suffix;

	/**
	 * Sets the layout template
	 *
	 * @param string $layout layout template path
	 * @return void
	 */
	public function layout( $layout ) {
		$this->layout = $layout;
	}

	/**
	 * Rendering the page
	 *
	 * @param string $view view template path
	 * @param string $view_suffix view template suffix
	 * @return void
	 */
	public function render( $view, $view_suffix = '' ) {
		$this->view        = $view;
		$this->view_suffix = $view_suffix;

		if ( is_singular() ) {
			$this->_render_loop();
		} else {
			$this->_render_direct();
		}
	}

	/**
	 * Rendering in the WordPress loop
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
	 * Rendering not in the WordPress loop
	 *
	 * @return void
	 */
	protected function _render_direct() {
		$this->_render();
	}

	/**
	 * Rendering the layout template
	 *
	 * @return void
	 */
	protected function _render() {
		$layout = apply_filters( 'inc2734_wp_view_controller_layout', $this->layout );
		$slug   = wpvc_config( 'layout' );
		wpvc_get_template_part( $slug . '/' . $layout, [
			'_view_controller' => $this,
		] );
	}

	/**
	 * Loading the view template in layout template
	 *
	 * @return void
	 */
	public function view() {
		$view = $this->_get_view_args();
		$view = apply_filters( 'inc2734_wp_view_controller_view', $view );
		get_template_part( $view['slug'], $view['name'] );
	}

	/**
	 * Gets the view args
	 *
	 * @return array
	 */
	protected function _get_view_args() {
		$slug = wpvc_config( 'view' );
		$view = [
			'slug' => $slug . '/' . $this->view,
			'name' => $this->view_suffix,
		];

		if ( is_404() || is_front_page() || is_search() ) {
			return $view;
		}

		$template_name = $this->get_static_view_template_name();

		if ( ! locate_template( $template_name . '.php', false ) ) {
			return $view;
		}

		return [
			'slug' => $template_name,
			'name' => '',
		];
	}

	/**
	 * Returns static view template name
	 *
	 * @return string
	 */
	public function get_static_view_template_name() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$request_uri = $this->_get_relative_path( $request_uri );
		$path        = $this->_remove_http_query( $request_uri );
		$path        = $this->_remove_paged_slug( $path );
		$path        = trim( $path, '/' );
		$slug        = wpvc_config( 'static' );
		return $slug . '/' . $path;
	}

	/**
	 * Return relative path from $uri
	 *
	 * @param string $uri
	 * @return string
	 */
	protected function _get_relative_path( $uri ) {
		return str_replace( home_url(), '', $uri );
	}

	/**
	 * Return uri that removed http queries
	 *
	 * @param string $uri
	 * @return string
	 */
	protected function _remove_http_query( $uri ) {
		$uri = str_replace( http_build_query( $_GET, null, '&' ), '', $uri );
		$uri = rtrim( $uri, '?' );
		return $uri;
	}

	/**
	 * Return uri that removed /page/xx/ and /paged/xx/
	 *
	 * @param string $uri
	 * @return string
	 */
	protected function _remove_paged_slug( $uri ) {
		if ( ! is_paged() ) {
			return $uri;
		}
		return preg_replace( '/^(.+?)\/page\/\d+/', '$1', $uri );
	}
}

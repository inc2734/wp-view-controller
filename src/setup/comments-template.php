<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_View_Controller\App\Config;
use Inc2734\WP_View_Controller\Helper;

add_filter(
	'comments_template',
	function( $theme_template ) {
		$slug = Helper::get_located_template_slug( Config::get( 'templates' ), 'comments' );
		return $slug ? $slug : $theme_template;
	}
);

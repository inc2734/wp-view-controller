<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_View_Controller\Helper;

add_filter(
	'comments_template',
	function( $theme_template ) {
		$template_name = Helper\locate_template( (array) Helper\config( 'templates' ), 'comments' );
		if ( is_null( $template_name ) ) {
			return;
		}

		return get_theme_file_path( '/' . $template_name . '.php' );
	}
);

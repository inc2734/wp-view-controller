<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

add_filter(
	'comments_template',
	function( $theme_template ) {
		$template_name = wpvc_locate_template( (array) wpvc_config( 'templates' ), 'comments' );
		if ( is_null( $template_name ) ) {
			return;
		}

		return get_theme_file_path( '/' . $template_name . '.php' );
	}
);

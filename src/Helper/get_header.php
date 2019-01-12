<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

/**
 * This function like get_header()
 *
 * @param string $name
 * @return void
 */
function get_header( $name = null ) {
	do_action( 'get_header', $name );

	if ( '' !== $name && file_exists( get_theme_file_path( "header-{$name}.php" ) ) ) {
		\locate_template( "header-{$name}.php", true, false );
		return;
	}
	if ( file_exists( get_theme_file_path( 'header.php' ) ) ) {
		\locate_template( 'header.php', true, false );
		return;
	}

	if ( $name ) {
		$template_name = locate_template( (array) config( 'templates' ), 'header-' . $name );
	}

	if ( empty( $template_name ) ) {
		$template_name = locate_template( (array) config( 'templates' ), 'header' );
	}

	if ( empty( $template_name ) ) {
		return;
	}

	get_template_part( $template_name );
}

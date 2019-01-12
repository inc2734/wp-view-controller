<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

/**
 * This function like get_sidebar()
 *
 * @param string $name
 * @return void
 */
function get_sidebar( $name = null ) {
	do_action( 'get_sidebar', $name );

	if ( '' !== $name && file_exists( get_theme_file_path( "sidebar-{$name}.php" ) ) ) {
		\locate_template( "sidebar-{$name}.php", true, false );
		return;
	}
	if ( file_exists( get_theme_file_path( 'sidebar.php' ) ) ) {
		\locate_template( 'sidebar.php', true, false );
		return;
	}

	if ( $name ) {
		$template_name = locate_template( (array) config( 'templates' ), 'sidebar-' . $name );
	}

	if ( empty( $template_name ) ) {
		$template_name = locate_template( (array) config( 'templates' ), 'sidebar' );
	}

	if ( empty( $template_name ) ) {
		return;
	}

	get_template_part( $template_name );
}

<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

/**
 * This function like get_footer()
 *
 * @param string $name
 * @return void
 */
function get_footer( $name = null ) {
	do_action( 'get_footer', $name );

	if ( '' !== $name && file_exists( get_theme_file_path( "footer-{$name}.php" ) ) ) {
		\locate_template( "footer-{$name}.php", true, false );
		return;
	}
	if ( file_exists( get_theme_file_path( 'footer.php' ) ) ) {
		\locate_template( 'footer.php', true, false );
		return;
	}

	if ( $name ) {
		$template_name = locate_template( (array) config( 'templates' ), 'footer-' . $name );
	}

	if ( empty( $template_name ) ) {
		$template_name = locate_template( (array) config( 'templates' ), 'footer' );
	}

	if ( empty( $template_name ) ) {
		return;
	}

	get_template_part( $template_name );
}

<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

use Inc2734\WP_View_Controller\App\Template_Part;

/**
 * A template tag that is get_template_part() using variables
 *
 * @param string $slug
 * @param string $name
 * @param array $vars
 * @return void
 */
function get_template_part( $slug, $name = null, array $vars = [] ) {
	/**
	 * Backward compatibility
	 */
	if ( is_array( $name ) && is_array( $vars ) && empty( $vars ) ) {
		$vars = $name;
		$name = null;
	}

	$template_part = new Template_Part( $slug, $name );
	$template_part->set_vars( $vars );
	$template_part->render();
}

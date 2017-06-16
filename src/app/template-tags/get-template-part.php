<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * A template tag that is get_template_part() using variables
 *
 * @param string $template
 * @param array $vars
 * @return void
 */
function wpvc_get_template_part( $template, $vars = [] ) {
	$template_part = new Inc2734_WP_View_Controller_Template_Part( $template );
	$template_part->set_vars( $vars );
	$template_part->render();
}

<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

return apply_filters(
	'inc2734_wp_view_controller_config',
	array(
		'templates'     => array( '' ),
		'page-template' => array( 'page-templates' ),
		'layout'        => array( 'templates/layout/wrapper' ),
		'header'        => array( 'templates/layout/header' ),
		'sidebar'       => array( 'templates/layout/sidebar' ),
		'footer'        => array( 'templates/layout/footer' ),
		'view'          => array( 'templates/view' ),
		'static'        => array( 'templates/static' ),
	)
);

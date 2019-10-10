<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * @deprecated
 */
$config = apply_filters(
	'inc2734_view_controller_config',
	[
		'templates'     => [ '' ],
		'page-template' => [ 'page-templates' ],
		'layout'        => [ 'templates/layout/wrapper' ],
		'header'        => [ 'templates/layout/header' ],
		'sidebar'       => [ 'templates/layout/sidebar' ],
		'footer'        => [ 'templates/layout/footer' ],
		'view'          => [ 'templates/view' ],
		'static'        => [ 'templates/static' ],
	]
);

return apply_filters( 'inc2734_wp_view_controller_config',$config );

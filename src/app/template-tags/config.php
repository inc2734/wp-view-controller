<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

/**
 * Getting config value
 *
 * @param string $slug the slug of the config file. e.g. config/directory
 * @param string $key the key of the config
 * @return mixed
 */
function wpvc_config( $slug, $key = null ) {
	return Inc2734_WP_View_Controller_Config::get( $slug, $key );
}

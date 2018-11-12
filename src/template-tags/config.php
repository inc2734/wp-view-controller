<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_View_Controller\App\Config_Loader;

/**
 * Getting config value
 *
 * @param string $key the key of the config
 * @return mixed
 */
function wpvc_config( $key = null ) {
	return Config_Loader::get( $key );
}

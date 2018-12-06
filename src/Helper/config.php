<?php
/**
 * @package inc2734/wp-view-controller
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Inc2734\WP_View_Controller\Helper;

use Inc2734\WP_View_Controller\App\Config;

/**
 * Getting config value
 *
 * @param string $key the key of the config
 * @return mixed
 */
function config( $key = null ) {
	return Config::get( $key );
}

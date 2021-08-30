<?php
/**
 * Plugin Name:     CAC Network Info
 * Plugin URI:      https://commons.gc.cuny.edu
 * Description:     Network theme/plugin tool for the CUNY Academic Commons
 * Author:          CUNY Academic Commons
 * Author URI:      https://commons.gc.cuny.edu
 * Text Domain:     cac-network-info
 * Version:         0.1.0
 * Network:         true
 *
 * @package         CacNetworkInfo
 */

define( 'CAC_NETWORK_INFO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAC_NETWORK_INFO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require __DIR__ . '/autoload.php';
cac_network_info()->init();

/**
 * Shorthand function to fetch our CAC Network Info instance.
 *
 * @since 0.1.0
 */
function cac_network_info() {
	return \CAC\NetworkInfo\App::get_instance();
}

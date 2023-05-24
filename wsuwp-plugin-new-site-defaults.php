<?php
/**
 * Plugin Name: WSUWP Plugin New Site Defaults
 * Plugin URI: https://github.com/wsuwebteam/wsuwp-plugin-new-site-defaults
 * Description: Configures site defaults when creating new sites
 * Version: 0.0.1
 * Requires PHP: 7.3
 * Author: Washington State University, Dan White
 * Author URI: https://web.wsu.edu/
 * Text Domain: wsuwp-plugin-new-site-defaults
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Initiate plugin
require_once __DIR__ . '/includes/plugin.php';

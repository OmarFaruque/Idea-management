<?php

/**
 * Plugin Name: Idea Management
 * Version: 1.0.0
 * Description: "Idea management help you to get idea from other user using a simple form. Also it's help you to vote on each idea from other user.".
 * Author: Oben
 * Author URI: https://github.com/OmarFaruque
 * Requires at least: 4.4.0
 * Tested up to: 6.0.1
 * Text Domain: idea-management
 */

define('IM_TOKEN', 'idea');
define('IM_VERSION', '1.0.0');
define('IM_FILE', __FILE__);
define('IM_PLUGIN_NAME', 'Idea Management');
define('IM_INIT_TIMESTAMP', gmdate( 'U' ) );
define('IM_PATH', realpath(plugin_dir_path(__FILE__)));


// Load and set up the Autoloader
require_once( dirname( __FILE__ ) . '/includes/class-awc-autoloader.php' );
$im_autoloader = new IM_Autoloader( dirname( __FILE__ ) );
$im_autoloader->register();

IM_Admin::instance(__FILE__);
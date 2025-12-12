<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * administrative area. This file also includes all of the plugin dependencies.
 *
 * @link              https://aethos.ai
 * @since             1.0.0
 * @package           Aethos_Chat
 *
 * @wordpress-plugin
 * Plugin Name:       Aethos AI Chatbot
 * Plugin URI:        https://aethos.ai
 * Description:       Aethos AI Chatbot WordPress Plugin
 * Version:           1.0.0
 * Author:            Aethos
 * Author URI:        https://aethos.ai
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aethos-chat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Enable debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't break output
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/aethos-debug.log');

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if ( ! defined( 'AETHOS_VERSION' ) ) {
    define( 'AETHOS_VERSION', '1.0.0' );
}

if ( ! defined( 'AETHOS_PLUGIN_URL' ) ) {
    define( 'AETHOS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'AETHOS_PLUGIN_DIR' ) ) {
    define( 'AETHOS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aethos-activator.php
 */
function activate_aethos_chat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aethos-activator.php';
	Aethos_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aethos-deactivator.php
 */
function deactivate_aethos_chat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aethos-deactivator.php';
	Aethos_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aethos_chat' );
register_deactivation_hook( __FILE__, 'deactivate_aethos_chat' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-aethos-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aethos_chat() {
    if ( class_exists( 'Aethos_Core' ) ) {
	    $plugin = new Aethos_Core();
	    $plugin->run();
    }
}
run_aethos_chat();

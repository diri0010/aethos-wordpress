<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * administrative area. This file also includes all of the plugin dependencies.
 *
 * @link              https://aethoslogic.com
 * @since             1.0.0
 * @package           Aethos_Chat
 *
 * @wordpress-plugin
 * Plugin Name:       Aethos AI Chatbot
 * Plugin URI:        https://aethoslogic.com
 * Description:       Aethos AI Chatbot WordPress Plugin
 * Version:           1.0.0
 * Author:            Aethos
 * Author URI:        https://aethoslogic.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aethos-chat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Log a message if WP_DEBUG is enabled.
 * Uses WordPress debug.log when WP_DEBUG_LOG is true.
 *
 * @param string $message The message to log.
 */
function aethos_log( $message ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[Aethos] ' . $message );
    }
}

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
 * Ensure REST API routes are registered early.
 * This is critical for widget endpoints to work.
 */
add_action( 'rest_api_init', function() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-aethos-rest-controller.php';
    $controller = new Aethos_REST_Controller();
    $controller->register_routes();
}, 5 ); // Priority 5 to run before other plugins

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

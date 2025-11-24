<?php
/**
 * Plugin Name:       Aethos AI Chat
 * Plugin URI:        https://aethos.chat
 * Description:       Integrate intelligent AI chatbots into your WordPress site.
 * Version:           1.0.0
 * Author:            Aethos AI
 * Author URI:        https://aethos.chat
 * License:           GPL-2.0+
 * Text Domain:       aethos-chat
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'AETHOS_VERSION', '1.0.0' );
define( 'AETHOS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AETHOS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

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
require plugin_dir_path( __FILE__ ) . 'includes/class-aethos-core.php';

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
	$plugin = new Aethos_Core();
	$plugin->run();
}
run_aethos_chat();

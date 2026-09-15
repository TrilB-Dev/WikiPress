<?php

/**
 * WikiPress - A WordPress Plugin
 *
 * This is the main plugin file for the WikiPress WordPress plugin. It contains the plugin metadata and initializes the plugin by including necessary files and setting up activation and deactivation hooks.
 *
 * Plugin Name:       WikiPress
 * Plugin URI:        https://trilb.dev/collection/web-extension/wordpress/wikipress
 * Description:       WikiPress is a WordPress plugin that provides a comprehensive wiki management system, allowing users to create, manage, and display wiki content within their WordPress site.
 * Version:           0.4.2-Dev
 * Author:            MrTrilB
 * Author URI:        https://trilb.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wikipress
 * Domain Path:       src/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WIKIPRESS_VERSION', '0.4.2-Dev' );
define( 'WIKIPRESS_NAME', 'wikipress' );
define( 'WIKIPRESS_DEFAULT_LANGUAGE', 'en_GB' );
define( 'WIKIPRESS_FILE', __FILE__ );
define( 'WIKIPRESS_DIR', plugin_dir_path( __FILE__ ) );
define( 'WIKIPRESS_URL', plugin_dir_url( __FILE__ ) );
define( 'WIKIPRESS_BASENAME', plugin_basename( __FILE__ ) );
define( 'WIKIPRESS_ROOT', WIKIPRESS_DIR );
define( 'WIKIPRESS_ROOT_URL', WIKIPRESS_URL );

$wikipress_includes_dir = is_dir( WIKIPRESS_DIR . 'src/includes' ) ? 'src/includes' : 'src/Includes';
$wikipress_includes_url = is_dir( WIKIPRESS_DIR . 'src/includes' ) ? 'src/includes' : 'src/Includes';
$wikipress_plugins_interface = file_exists( WIKIPRESS_DIR . 'src/includes/Plugins/PluginsInterface.php' )
	? WIKIPRESS_DIR . 'src/includes/Plugins/PluginsInterface.php'
	: WIKIPRESS_DIR . 'src/Includes/Plugins/PluginsInterface.php';

define( 'WIKIPRESS_API', WIKIPRESS_DIR . 'src/API' );
define( 'WIKIPRESS_ASSETS', WIKIPRESS_DIR . 'src/Assets' );
define( 'WIKIPRESS_ASSETS_URL', WIKIPRESS_URL . 'src/Assets' );
define( 'WIKIPRESS_ADMIN', WIKIPRESS_DIR . 'src/Admin' );
define( 'WIKIPRESS_ADMIN_URL', WIKIPRESS_URL . 'src/Admin' );
define( 'WIKIPRESS_LANGUAGES', WIKIPRESS_DIR . 'src/languages' );
define( 'WIKIPRESS_INCLUDES', WIKIPRESS_DIR . $wikipress_includes_dir );
define( 'WIKIPRESS_CORE', WIKIPRESS_INCLUDES . '/Core' );
define( 'WIKIPRESS_SETTINGS', WIKIPRESS_INCLUDES . '/Settings' );
define( 'WIKIPRESS_PLUGINS', WIKIPRESS_INCLUDES . '/Plugins' );
define( 'WIKIPRESS_PLUGINS_URL', WIKIPRESS_URL . $wikipress_includes_url . '/Plugins' );

$wikipress_autoloader = WIKIPRESS_DIR . 'vendor/autoload.php';
if ( is_readable( $wikipress_autoloader ) ) {
	require_once $wikipress_autoloader;
}

spl_autoload_register(
	static function ( string $class_name ): void {
		if ( 0 !== strpos( $class_name, 'WikiPress\\' ) ) {
			return;
		}

		$relative_path = str_replace( '\\', '/', substr( $class_name, strlen( 'WikiPress\\' ) ) ) . '.php';
		$candidates    = array(
			WIKIPRESS_DIR . 'src/includes/' . $relative_path,
			WIKIPRESS_DIR . 'src/Includes/' . $relative_path,
			WIKIPRESS_DIR . 'src/' . $relative_path,
		);

		foreach ( $candidates as $candidate ) {
			if ( is_readable( $candidate ) ) {
				require_once $candidate;
				return;
			}
		}
	},
	true,
	true
);

if ( is_readable( $wikipress_plugins_interface ) ) {
	require_once $wikipress_plugins_interface;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in Includes/class-pluginname-activator.php
 */
function activate_wikipress() {
	\WikiPress\Includes\Core\WP\Activator::activate();
}

register_activation_hook( __FILE__, 'activate_wikipress' );
/**
 * The code that runs during plugin deactivation.
 * This action is documented in Includes/class-pluginname-deactivator.php
 */
function deactivate_wikipress() {
	\WikiPress\Includes\Core\WP\Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_wikipress' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once WIKIPRESS_DIR . 'src/WikiPress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wikipress() {

	$plugin = new \WikiPress\WikiPress( WIKIPRESS_FILE, WIKIPRESS_NAME, WIKIPRESS_VERSION );
	$plugin->run();

}
run_wikipress();

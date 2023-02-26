<?php
/**
 * Plugin Name: WP Theme Migrator
 * Plugin URI: https://github.com/alleyinteractive/wp-theme-migrator
 * Description: A WordPress plugin to migrate to a new theme incrementally.
 * Version: 0.1.0
 * Author: Alley
 * Author URI: https://alley.com
 * Requires at least: 6.1
 * Tested up to: 6.1.1
 * Requires PHP: 8.0
 *
 * Text Domain: wp-theme-migrator
 * Domain Path: /languages/
 *
 * @package wp-theme-migrator
 */

namespace Alley\WP\Theme_Migrator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Root directory to this plugin.
 *
 * @var string
 */
define( 'WP_THEME_MIGRATOR_DIR', __DIR__ );

// Check if Composer is installed.
if ( ! file_exists( __DIR__ . '/vendor/wordpress-autoload.php' ) ) {
	add_action(
		'admin_notices',
		function() {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'Composer is not installed and WP Theme Migrator cannot load. Try using a `*-built` branch if the plugin is being loaded as a submodule.', 'wp-theme-migrator' ); ?></p>
			</div>
			<?php
		}
	);

	return;
}

// Load Composer dependencies.
require_once __DIR__ . '/vendor/wordpress-autoload.php';

/**
 * Instantiate the plugin.
 */
function main() {
	$migrator = new Migrator();

	/**
	 * This action fires before the migrator has been initialized.
	 *
	 * @param Migrator $migrator Migrator.
	 */
	do_action( 'wp_theme_migrator_before_init', $migrator );

	$migrator->set_context(
		/**
		 * Filters context values.
		 *
		 * @param array    $context Array of context values.
		 * @param Migrator $migrator Migrator instance.
		 * @return array Filtered array of context values.
		 */
		apply_filters( 'wp_theme_migrator_context', [], $migrator )
	);

	/**
	 * This action fires after the migrator has been initialized.
	 *
	 * @param Migrator $migrator Migrator.
	 */
	do_action( 'wp_theme_migrator_after_init', $migrator );
}

main();

<?php
/**
 * Bootstrap.
 *
 * @package wp_theme_migrator
 */

\Mantle\Testing\manager()
	->maybe_rsync_plugin()
	// Load the main file of the plugin.
	->loaded( fn () => require_once __DIR__ . '/../wp-theme-migrator.php' )
	->install();

<?php
/**
 * Bootstrap.
 *
 * @package wp_theme_migrator
 */

declare(strict_types = 1);

use Symfony\Component\Filesystem\Filesystem;
use function Mantle\Testing\manager;

// Run unit tests.
manager()
	->loaded(
		function() {
			// Load test themes.
			$file_system = new Filesystem();
			$file_system->mirror( __DIR__ . '/themes', get_theme_root() );

			switch_theme( 'original' );
		}
	)
	->install();

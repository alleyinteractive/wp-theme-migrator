<?php
/**
 * Bootstrap.
 *
 * @package wp_theme_migrator
 */

//  Mantle\Testing\install();
\Mantle\Testing\manager()
	->loaded(
		function() {
			if ( ! defined( 'ABSPATH' ) ) {
				return;
			}

			// Load test themes.
			$file_system = new Symfony\Component\Filesystem\Filesystem();
			$file_system->mirror( __DIR__ . '/themes', get_theme_root() );

			switch_theme( 'original' );
		}
	)
	->install();

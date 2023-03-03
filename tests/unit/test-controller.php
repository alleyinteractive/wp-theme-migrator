<?php
/**
 * Test_Controller class file.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator\Tests;

use Alley\WP\Theme_Migrator\Context;
use Alley\WP\Theme_Migrator\Controller;

/**
 * Visit {@see https://phpunit.de/documentation.html} to learn more.
 */
class Test_Controller extends Test_Case {
	public function setUp(): void {
		$this->controller = new Controller(
			new Context(
				[
					'from_theme' => 'theme-a',
					'to_theme'   => 'twentytwentythree',
					'callbacks'  => [ '__return_true' ],
				]
			)
		 );

		parent::setUp();
	}

	public function test_switch_theme() {
		// $this->assertNotSame( 'twentytwentythree', basename( STYLESHEETPATH ) );
		// $this->assertNotSame( 'twentytwentythree', basename( TEMPLATEPATH ) );
		// $this->assertNotSame( 'twentytwentythree', get_option( 'stylesheet' ) );
		// $this->assertNotSame( 'twentytwentythree', get_option( 'template' ) );
		// $this->assertNotSame( 'twentytwentythree', get_option( 'current_theme' ) );

		$this->controller->switch_theme();

		$this->assertSame( 'twentytwentythree', basename( STYLESHEETPATH ) );
		$this->assertSame( 'twentytwentythree', basename( TEMPLATEPATH ) );
		$this->assertSame( 'twentytwentythree', get_option( 'stylesheet' ) );
		$this->assertSame( 'twentytwentythree', get_option( 'template' ) );
		$this->assertSame( wp_get_theme( 'twentytwentythree' )?->get( 'Name' ) ?? '', get_option( 'current_theme' ) );
	}

}

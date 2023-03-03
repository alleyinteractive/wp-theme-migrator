<?php
/**
 * Test_Context class file.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator\Tests;

use Alley\WP\Theme_Migrator\Context;

/**
 * Visit {@see https://phpunit.de/documentation.html} to learn more.
 */
class Test_Context extends Test_Case {
	public function test_constructor() {
		$this->context = new Context(
			[
				'from_theme' => 'theme-a',
				'to_theme'   => 'theme-b',
				'callbacks'  => [ '__return_true' ],
			]
		);

		$this->assertSame( $this->context->from_theme, 'theme-a' );
		$this->assertSame( $this->context->to_theme, 'theme-b' );
		$this->assertEquals( $this->context->callbacks, [ '__return_true' ] );
	}

	public function test_is_valid_context() {
		$this->context = new Context();
		$this->assertFalse( $this->context->is_valid_context( [] ) );
		$this->assertFalse( $this->context->is_valid_context( [ 'from_theme' => '', 'to_theme' => '', 'callbacks' => [] ] ) );
		$this->assertTrue( $this->context->is_valid_context( [ 'from_theme' => 'theme-a', 'to_theme' => 'theme-b', 'callbacks' => [ '__return_true' ] ] ) );
	}
}

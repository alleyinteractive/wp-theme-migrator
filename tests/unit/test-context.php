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
	public function setUp(): void {
		parent::setUp();
	}

	public function test_properties() {
		$callbacks = [
			[
				'callback' => 'callback-a',
				'args'     => [
					[
						'arg-1',
					]
				]
			]
					];

		$this->context = new Context(
			[
				'from_theme' => 'theme-a',
				'to_theme' => 'theme-b',
				'callbacks' => $callbacks,
			]
		);

		$this->assertSame( $this->context->from_theme, 'theme-a' );
		$this->assertSame( $this->context->to_theme, 'theme-b' );
		$this->assertEquals( $this->context->callbacks, $callbacks );
	}
}

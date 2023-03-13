<?php
/**
 * Test_Context class file.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator\Tests;

use Alley\WP\Theme_Migrator\Context;
use ReflectionClass;

/**
 * Visit {@see https://phpunit.de/documentation.html} to learn more.
 */
class Test_Context extends Test_Case {
	/**
	 * Data provider for test_is_valid_context method.
	 *
	 * @return array Array of data.
	 */
	public function data_is_valid_context(): array {
		return [
			'valid theme and empty callbacks' => [
				[
					'theme'     => 'classic-theme',
					'callbacks' => [],
				],
				true,
			],
			'valid theme and missing callbacks' => [
				[
					'theme' => 'classic-theme',
				],
				true,
			],
			'valid theme and one valid callback' => [
				[
					'theme'     => 'classic-theme',
					'callbacks' => [
						'__return_true',
					],
				],
				true,
			],
			'valid theme and two valid callbacks' => [
				[
					'theme'     => 'classic-theme',
					'callbacks' => [
						'__return_true',
						'__return_false',
					],
				],
				true,
			],
			'valid theme and one invalid callback' => [
				[
					'theme'     => 'classic-theme',
					'callbacks' => [
						'not_a_valid_callback',
					],
				],
				false,
			],
			'valid theme and mixed callbacks' => [
				[
					'theme'     => 'classic-theme',
					'callbacks' => [
						'__return_true',
						'not_a_valid_callback',
					],
				],
				false,
			],
			'invalid theme and missing callbacks' => [
				[
					'theme' => 'not-a-theme',
				],
				false,
			],
		];
	}

	/**
	 * Tests the functionality of the is_valid_context method.
	 *
	 * @dataProvider data_is_valid_context
	 *
	 * @param array $original Array of context constructor args to test.
	 * @param bool  $expected The expected result.
	 */
	public function test_is_valid_context( array $original, bool $expected ) {
		$context = new Context( $original );
		$this->assertEquals( $expected, $context->is_valid_context() );
	}
}

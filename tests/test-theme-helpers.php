<?php
/**
 * Test_Theme_Helpers class file.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator\Tests;

use Alley\WP\Theme_Migrator\Context;
use ReflectionClass;

/**
 * Visit {@see https://phpunit.de/documentation.html} to learn more.
 */
class Test_Theme_Helpers extends Test_Case {
    use \Alley\WP\Theme_Migrator\Theme_Helpers;

    /**
	 * Data provider for test_is_valid_theme method.
	 *
	 * @return array Array of data.
	 */
	public function data_is_valid_theme(): array {
		return [
			'a classic theme' => [
				'classic-theme-a',
				true,
			],
			'a nonexistent theme' => [
				'not-a-theme',
				false,
			],
			'a WP-incompatible theme' => [
				'wp-incompatible-theme',
				false,
			],
			'a PHP-incompatible theme' => [
				'php-incompatible-theme',
				false,
			],
		];
	}

	/**
	 * Tests the functionality of the is_valid_theme method.
	 *
	 * @dataProvider data_is_valid_theme
	 *
	 * @param string $original The theme name to test.
	 * @param bool   $expected The expected result.
	 *
	 * @throws ReflectionException
	 */
	public function test_is_valid_theme( string $original, bool $expected ) {
		$this->assertEquals( $expected, $this->is_valid_theme( $original ) );
	}
}

<?php
/**
 * Test_Theme_Helpers class file.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator\Tests;

use Alley\WP\Theme_Migrator\Theme_Helpers;
use ReflectionClass;

/**
 * Visit {@see https://phpunit.de/documentation.html} to learn more.
 */
class Test_Theme_Helpers extends Test_Case {
	/**
	 * Data provider for test_is_valid_theme method.
	 *
	 * @return array Array of data.
	 */
	public function data_is_valid_theme(): array {
		return [
			'valid classic theme' => [
				'classic-theme',
				true,
			],
			'missing theme' => [
				'missing-theme',
				false,
			],
			'PHP-incompatible theme' => [
				'php-incompatible-theme',
				false,
			],
			'WP-incompatible theme' => [
				'wp-incompatible-theme',
				false,
			],
		];
	}

	/**
	 * Tests the functionality of the is_valid_theme method.
	 *
	 * @dataProvider data_is_valid_theme
	 *
	 * @param string $original Slug of theme to test.
	 * @param bool   $expected The expected result.
	 *
	 * @throws ReflectionException
	 */
	public function test_is_valid_theme( string $original, bool $expected ) {
		// Instantiate anonymous class to test trait.
		$object = new class {
			use Theme_Helpers;
		};

		$class  = new ReflectionClass( get_class( $object ) );
		$method = $class->getMethod( 'is_valid_theme' );
		$method->setAccessible( true );

		$this->assertEquals( $expected, $method->invoke( $object, $original ) );
	}
}

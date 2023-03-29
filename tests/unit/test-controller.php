<?php
/**
 * Test_Controller class file.
 *
 * @package wp_theme_migrator
 */

declare(strict_types = 1);

namespace Alley\WP\Theme_Migrator\Tests;

use Alley\WP\Theme_Migrator\Context;
use Alley\WP\Theme_Migrator\Controller;
use ReflectionClass;

/**
 * Visit {@see https://phpunit.de/documentation.html} to learn more.
 */
class Test_Controller extends Test_Case {
	/**
	 * Set up.
	 */
	public function setup(): void {
		$this->valid_context = new Context(
			theme: 'classic-theme',
			callbacks: [ '__return_true' ],
		);

		parent::setup();
	}

	/**
	 * Tests the functionality of the set_up_wp method.
	 *
	 * @throws ReflectionException Thrown if the class to reflect does not exist.
	 */
	public function test_set_up_wp() {
		$controller = new Controller( $this->valid_context );

		$class  = new ReflectionClass( 'Alley\WP\Theme_Migrator\Controller' );
		$method = $class->getMethod( 'set_up_wp' );
		$method->setAccessible( true );

		$property = $class->getProperty( 'wp' );
		$property->setAccessible( true );

		$this->go_to( home_url( '/asdf/' ) );
		$method->invoke( $controller );

		$this->assertNotEmpty( $property->getValue( $controller )->query_vars );
	}

	/**
	 * Data provider for test_set_migratability_during_cron method.
	 *
	 * @return array Array of data.
	 */
	public function data_set_migratability_during_cron(): array {
		return [
			'skip during cron'        => [
				'__return_true',
				false,
			],
			'don\'t skip during cron' => [
				'__return_false',
				true,
			],
		];
	}

	/**
	 * Tests the functionality of the set_migratability method while DOING_CRON.
	 *
	 * @dataProvider data_set_migratability_during_cron
	 *
	 * @param callable $original The filter callback to test.
	 * @param bool     $expected The expected result.
	 *
	 * @throws ReflectionException Thrown if the class to reflect does not exist.
	 */
	public function test_set_migratability_during_cron( callable $original, bool $expected ) {
		if ( ! defined( 'DOING_CRON' ) ) {
			define( 'DOING_CRON', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
		}

		$controller = new Controller( $this->valid_context );

		$class  = new ReflectionClass( 'Alley\WP\Theme_Migrator\Controller' );
		$method = $class->getMethod( 'set_migratability' );
		$method->setAccessible( true );

		$property = $class->getProperty( 'should_migrate' );
		$property->setAccessible( true );

		add_filter( 'wp_theme_migrator_skip_during_cron', $original );

		$method->invoke( $controller );

		$this->assertEquals( $expected, $property->getValue( $controller ) );
	}

	/**
	 * Data provider for test_check_migratability method.
	 *
	 * @return array Array of data.
	 */
	public function data_check_migratability(): array {
		return [
			'a migrateable callback'    => [
				[
					'__return_true',
				],
				true,
			],
			'an unmigrateable callback' => [
				[
					'__return_false',
				],
				false,
			],
			'a migrateable callback and an unmigrateable callback' => [
				[
					'__return_true',
					'__return_false',
				],
				true,
			],
			'an unmigrateable callback and a migrateable callback' => [
				[
					'__return_false',
					'__return_true',
				],
				true,
			],
			'no callbacks'              => [
				[],
				false,
			],
		];
	}

	/**
	 * Tests the functionality of the check_migratability method.
	 *
	 * @dataProvider data_check_migratability
	 *
	 * @param callable[] $original The array of callback to test.
	 * @param bool       $expected The expected result.
	 *
	 * @throws ReflectionException Thrown if the class to reflect does not exist.
	 */
	public function test_check_migratability( array $original, bool $expected ) {
		$controller = new Controller(
			new Context(
				theme: 'classic-theme',
				callbacks: $original,
			)
		);

		$class  = new ReflectionClass( 'Alley\WP\Theme_Migrator\Controller' );
		$method = $class->getMethod( 'check_migratability' );
		$method->setAccessible( true );

		$this->assertEquals( $expected, $method->invoke( $controller ) );
	}

	/**
	 * Data provider for test_switch_theme method.
	 *
	 * @return array Array of data.
	 */
	public function data_switch_theme(): array {
		return [
			'a theme' => [
				[
					'theme'     => 'classic-theme',
					'callbacks' => [ '__return_true' ],
				],
				true,
			],
		];
	}

	/**
	 * Tests the functionality of the switch_theme method.
	 *
	 * @dataProvider data_switch_theme
	 *
	 * @param array $original The context to test.
	 * @param bool  $expected The expected result.
	 *
	 * @throws ReflectionException Thrown if the class to reflect does not exist.
	 */
	public function test_switch_theme( array $original, bool $expected ) {
		$controller = new Controller( new Context( $original['theme'] ?? '', $original['callbacks'] ?? [] ) );

		$class  = new ReflectionClass( 'Alley\WP\Theme_Migrator\Controller' );
		$method = $class->getMethod( 'switch_theme' );
		$method->setAccessible( true );

		$method->invoke( $controller );

		$this->assertEquals( $expected, has_filter( 'option_stylesheet', [ $controller, 'get_theme_stylesheet' ] ) );
		$this->assertEquals( $expected, has_filter( 'option_template', [ $controller, 'get_theme_template' ] ) );
		$this->assertEquals( $expected, has_filter( 'option_current_theme', [ $controller, 'get_theme_name' ] ) );
	}
}

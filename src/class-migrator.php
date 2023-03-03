<?php
/**
 * Migrator class file
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator;

use Alley\WP\Theme_Migrator\Context;
use Alley\WP\Theme_Migrator\Contoller;

/**
 * Migrator class.
 */
class Migrator {
	/**
	 * Context instance.
	 *
	 * @var Context
	 */
	protected $context;

	/**
	 * Controller instance.
	 *
	 * @var Controller
	 */
	protected $controller;

	/**
	 * Initializes Migrator.
	 */
	public function init() {
		$this->context = new Context(
			/**
			 * Filters context values.
			 *
			 * @param array    $context Array of context values.
			 * @param Migrator $migrator This Migrator instance.
			 */
			apply_filters( 'wp_theme_migrator_context', [], $this )
		);

		$this->controller = new Controller( $this->context );
	}
}

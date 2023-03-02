<?php
/**
 * Migrator class file
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator;

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
		$this->controller = new Controller( $this->context );
	}

	/**
	 * Sets the Context.
	 *
	 * @param array $context Array of Context values.
	 */
	public function set_context( array $context ) {
		$this->context = new Context( $context );
	}

	/**
	 * Gets the Context.
	 *
	 * @return Context
	 */
	public function get_context(): Context {
		return $this->context;
	}
}

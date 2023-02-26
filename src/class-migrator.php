<?php
/**
 * Migrator class file
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator;

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
	 * Sets the Context.
	 *
	 * @param array $context Array of Context values.
	 */
	public function set_context( array $context ) {
		$this->context = new Context( ...$context );
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

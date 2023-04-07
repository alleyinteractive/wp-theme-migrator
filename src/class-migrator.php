<?php
/**
 * Migrator class file
 *
 * @package wp_theme_migrator
 */

declare(strict_types = 1);

namespace Alley\WP\Theme_Migrator;

use Alley\WP\Theme_Migrator\Context;
use Alley\WP\Theme_Migrator\Controller;

/**
 * Migrator class.
 */
class Migrator {
	/**
	 * Context instance.
	 *
	 * @var Context
	 */
	protected Context $context;

	/**
	 * Controller instance.
	 *
	 * @var Controller
	 */
	protected Controller $controller;

	/**
	 * Initializes Migrator.
	 */
	public function init() {
		$this->context = new Context(
			/**
			 * Filters context theme.
			 *
			 * @param string   $theme Slug of theme to migrate to.
			 * @param Migrator $migrator This Migrator instance.
			 */
			theme: apply_filters( 'wp_theme_migrator_theme', '', $this ),
			/**
			 * Filters context callbacks.
			 *
			 * @param callable[] $callbacks Array of callbacks to determine migratability.
			 * @param Migrator   $migrator This Migrator instance.
			 */
			callbacks: apply_filters( 'wp_theme_migrator_callbacks', [], $this ),
		);

		$this->controller = new Controller( $this->context );
	}
}

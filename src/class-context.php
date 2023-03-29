<?php
/**
 * Context class file.
 *
 * @package wp_theme_migrator
 */

declare(strict_types = 1);

namespace Alley\WP\Theme_Migrator;

/**
 * Context class.
 */
class Context {
	use \Alley\WP\Theme_Migrator\Theme_Helpers;

	/**
	 * Constructor.
	 *
	 * @param string     $theme     Slug of theme to migrate to.
	 * @param callable[] $callbacks Array of callbacks to determine
	 * migratability during a request. If any callback in the array returns true
	 * for the current request then the request will be loaded with the new theme.
	 */
	public function __construct(
		protected string $theme = '',
		protected array $callbacks = [],
	) {}

	/**
	 * Check if context args are valid.
	 *
	 * @return bool Valid or not.
	 */
	public function is_valid_context(): bool {
		// A valid context must have a valid theme.
		if ( ! $this->is_valid_theme( $this->theme ) ) {
			return false;
		}

		// If callbacks exist, they must be valid callables.
		foreach ( $this->callbacks as $callback ) {
			if ( ! is_callable( $callback ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the slug of the theme we're migrating to.
	 *
	 * @return string Theme slug.
	 */
	public function get_theme_slug(): string {
		return $this->theme;
	}

	/**
	 * Get the name of the theme we're migrating to.
	 *
	 * @return string Theme name.
	 */
	public function get_theme_name(): string {
		return $this->get_theme( $this->theme, 'name' );
	}

	/**
	 * Get array of callbacks.
	 *
	 * @return callable[] Array of callbacks.
	 */
	public function get_callbacks(): array {
		return $this->callbacks;
	}
}

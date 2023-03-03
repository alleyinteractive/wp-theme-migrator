<?php
/**
 * Context class file.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator;

/**
 * Context class.
 */
class Context {
	/**
	 * Theme slug to migrate from.
	 *
	 * @var string
	 */
	public $from_theme;

	/**
	 * Theme slug to migrate to.
	 *
	 * @var string
	 */
	public $to_theme;

	/**
	 * Array of callbacks to determine migratability during a request. If
	 * any callback in the array returns true for the current request then the
	 * request will be loaded with the new theme.
	 *
	 * @var callable[]
	 */
	public $callbacks;

	/**
	 * Constructor.
	 *
	 * @param array $args Array of context args.
	 *  [
	 *      string     $from_theme Theme slug to migrate from.
	 *      string     $to_theme Theme slug to migrate to.
	 *      callable[] $callbacks Array of callbacks.
	 *  ].
	 */
	public function __construct( array $args = [] ) {
		if ( ! $this->is_valid_context( $args ) ) {
			// @todo Add link to README with info on setup.
			add_action(
				'admin_notices',
				function() {
					?>
					<div class="notice notice-error">
						<p><?php esc_html_e( 'WP Theme Migrator requires setup to work properly.', 'wp-theme-migrator' ); ?></p>
					</div>
					<?php
				}
			);
			return;
		}

		$this->from_theme = $args['from_theme'] ?? '';
		$this->to_theme   = $args['to_theme'] ?? '';
		$this->callbacks  = $args['callbacks'] ?? [];

		add_action(
			'admin_notices',
			function() {
				?>
				<div class="notice notice-info">
					<p><?php esc_html_e( 'WP Theme Migrator is active. Requests may load with a different theme.', 'wp-theme-migrator' ); ?></p>
				</div>
				<?php
			}
		);
	}

	/**
	 * Check if context args are valid.
	 *
	 * See Constructor for $args properties.
	 *
	 * @param array $args Array of context args.
	 * @return bool Valid or not.
	 */
	public function is_valid_context( array $args = [] ): bool {
		if ( empty( $args['from_theme'] ) || empty( $args['to_theme'] ) ) {
			return false;
		}

		foreach ( $args['callbacks'] as $callback ) {
			if ( ! is_callable( $callback ) ) {
				return false;
			}
		}

		return true;
	}
}

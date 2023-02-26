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
	 * Constructor.
	 *
	 * @param string $from_theme Theme slug to migrate from.
	 * @param string $to_theme Theme slug to migrate to.
	 */
	public function __construct( string $from_theme = '', string $to_theme = '' ) {
		if ( ! $this->is_valid_context( $from_theme, $to_theme ) ) {
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

		$this->from_theme = $from_theme;
		$this->to_theme   = $to_theme;
	}

	/**
	 * Check if context args are valid.
	 *
	 * @param string $from_theme Theme slug to migrate from.
	 * @param string $to_theme Theme slug to migrate to.
	 * @return bool Valid or not.
	 */
	protected function is_valid_context( string $from_theme = '', string $to_theme = '' ): bool {
		return ! ( empty( $from_theme ) && empty( $to_theme ) );
	}
}

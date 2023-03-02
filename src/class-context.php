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
	 * Nested array of callbacks to determine migratability during a request. If
	 * any callback in the array returns true for the current request then the
	 * request will be loaded with the new theme.
	 *
	 * @var array
	 * [
	 * 		[
	 * 			callable $callback Name of callback.
	 * 			array    $args Array of parameters to be passed to callback.
	 * 		]
	 * ]
	 */
	public $callbacks;

	/**
	 * Constructor.
	 *
	 * @param $args Array of context args.
	 *	[
	 * 		string $from_theme Theme slug to migrate from.
	 *		string $to_theme Theme slug to migrate to.
	 *		array  $callbacks Nested array of callbacks and args.
	 * 	]
	 */
	public function __construct( array $args = [] ) {
		if ( ! $this->is_valid_context( $args ) ) {
			// @todo Add link to README with info on setup.
			$this->notify( 'error', __( 'WP Theme Migrator requires setup to work properly.', 'wp-theme-migrator' ) );
			return;
		}

		$this->from_theme = $args['from_theme'] ?? '';
		$this->to_theme   = $args['to_theme'] ?? '';
		$this->callbacks  = $args['callbacks'] ?? [];

		$this->notify( 'info', __( 'WP Theme Migrator is active. Requests may load with a different theme.', 'wp-theme-migrator' ) );
	}

	/**
	 * Add an admin notice.
	 *
	 * @param string $type Notice type.
	 * @param string $message Notice message.
	 */
	protected function notify( string $type, string $message ) {
		add_action(
			'admin_notices',
			function() {
				?>
				<div class="<?php echo esc_attr( "notice notice-${type}" ); ?>">
					<p><?php echo esc_html( $message ); ?></p>
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
	 * @param $args Array of context args.
	 * @return bool Valid or not.
	 */
	protected function is_valid_context( array $args = [] ): bool {
		if ( empty( $args['from_theme'] ) || empty( $args['to_theme'] ) ) {
			return false;
		}

		if ( ! empty( $args['callbacks'] ) ) {
			foreach ( $args['callbacks'] as $callback ) {
				if ( empty( $callback['callback'] ) ) {
					return false;
				}

				if ( ! is_callable( $callback['callback'] ) ) {
					return false;
				}

				if ( ! empty( $callback['args'] ) && ! is_array( $callback['args'] ) ) {
					return false;
				}
			}
		}

		return true;
	}
}

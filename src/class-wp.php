<?php
/**
 * WP class file. Extends the WordPress core WP class. By doing this, we can
 * parse the request early and provide query vars to determine migratability
 * before the theme is loaded.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator;

/**
 * WP class.
 */
class WP extends \WP {
	/**
	 * Sets up all of the variables required by the WordPress environment.
	 *
	 * @param string|array $query_args Passed to parse_request().
	 */
	public function main( $query_args = '' ) {
		$parsed = $this->parse_request( $query_args );

		if ( false === $parsed ) {
			error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				esc_html__( 'WP Theme Migrator failed. Unable to parse request.', 'wp-theme-migrator' )
			);
		}
	}
}

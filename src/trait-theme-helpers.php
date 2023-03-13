<?php
/**
 * Theme_Helpers trait file.
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator;

use WP_Theme;

/**
 * Theme_Helpers trait.
 */
trait Theme_Helpers {
	/**
	 * Validate theme. A theme is valid if it exists in the theme directory and
	 * if it is compatible with the current version of PHP and WordPress.
	 *
	 * @param string $theme Theme slug.
	 * @return bool Whether theme is valid.
	 */
	protected function is_valid_theme( string $theme ): bool {
		// Check for minimum PHP and WP versions. Returns true if version is
		// greater than the minimum or is not specified.
		$requirements = validate_theme_requirements( $theme );
		if ( is_wp_error( $requirements ) ) {
			return false;
		}

		// Check that the theme exists.
		if ( false === ( wp_get_theme( $theme )?->exists() ?? false ) ) {
			return false;
		}

		return true;
	}

	protected function get_theme( string $theme, string $prop = '' ) {
		if ( empty( $theme ) ) {
			return '';
		}

		switch ( $prop ) {
			case 'slug':
				return $theme;
			case 'name':
				return wp_get_theme( $theme )?->get( 'Name' ) ?? '';
			default:
				return wp_get_theme( $theme );
		}
	}

}

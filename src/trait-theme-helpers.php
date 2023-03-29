<?php
/**
 * Theme_Helpers trait file.
 *
 * @package wp_theme_migrator
 */

declare(strict_types = 1);

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

	/**
	 * Get theme data. Returns WP_Theme object if $prop is omitted.
	 *
	 * @param string $theme Slug of theme to get.
	 * @param string $prop  Name of property to get.
	 * @return string|WP_Theme Value.
	 */
	protected function get_theme( string $theme, string $prop = '' ) {
		if ( empty( $theme ) ) {
			return '';
		}

		switch ( $prop ) {
			case 'name':
				return wp_get_theme( $theme )?->get( 'Name' ) ?? '';
			default:
				return wp_get_theme( $theme );
		}
	}

}

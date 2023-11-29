<?php
/**
 * Controller class file
 *
 * @package wp_theme_migrator
 */

declare(strict_types = 1);

namespace Alley\WP\Theme_Migrator;

use Alley\WP\Theme_Migrator\Context;
use Alley\WP\Theme_Migrator\WP as WP_Theme_Migrator_WP;
use Exception;

/**
 * Controller class.
 */
class Controller {
	use \Alley\WP\Theme_Migrator\Theme_Helpers;

	/**
	 * WP_Theme_Migrator_WP instance.
	 *
	 * @var WP_Theme_Migrator_WP
	 */
	protected WP_Theme_Migrator_WP $wp;

	/**
	 * Constructor.
	 *
	 * @param Context $context Context object.
	 * @param bool    $should_migrate Whether to switch to the new theme for the
	 * current request.
	 *
	 * @throws Exception Thrown if context is not valid.
	 */
	public function __construct(
		protected Context $context,
		protected bool $should_migrate = false,
	) {
		if ( ! $context->is_valid_context() ) {
			throw new Exception(
				// @todo Add more robust error messaging.
				sprintf(
					esc_html__( 'WP Theme Migrator failed. Context is not valid.', 'wp-theme-migrator' ),
				)
			);
		}

		$this->wp = new WP_Theme_Migrator_WP();
		$this->init();
	}

	/**
	 * Add hooks to kick off migrator and tie in useful query vars.
	 */
	protected function init() {
		/**
		 * The callback to wp_enable_block_templates() in core eventually sets
		 * up a static variable that indicates whether the theme has a
		 * theme.json. Our priority is set at 8 so we can determine which
		 * theme we're using before that happens.
		 *
		 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/default-filters.php#L710
		 *
		 * Custom post types and taxonomies must be registered before the
		 * `run()` callback is run to be available for determining
		 * migratability.
		 */
		add_action( 'setup_theme', [ $this, 'run' ], 8 );

		// Add query vars for post types to our WP.
		add_action( 'registered_post_type', [ $this, 'add_post_type_query_vars' ], 10, 2 );

		// @todo Add query vars for taxonomies.
	}

	/**
	 * Run the migrator.
	 */
	public function run() {
		// Set up our extended WP instance to establish query vars early.
		$this->set_up_wp();

		// Determine migratability of the current request.
		$this->set_migratability();

		// Maybe migrate.
		$this->maybe_migrate();
	}

	/**
	 * Sets up WP_Theme_Migrator_WP instance and parses the request.
	 */
	protected function set_up_wp() {
		$this->wp->main();
	}

	/**
	 * Set migratability of the current request.
	 */
	protected function set_migratability() {
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			/**
			 * Skip migration checks while doing cron.
			 *
			 * @param bool              $skip_during_cron Whether to skip.
			 * @param WP_Theme_Migrator $this This WP_Theme_Migrator instance.
			 */
			if ( apply_filters( 'wp_theme_migrator_skip_during_cron', true, $this ) ) {
				return;
			}
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			/**
			 * Skip migration checks while doing Ajax.
			 *
			 * @param bool              $skip_during_ajax Whether to skip.
			 * @param WP_Theme_Migrator $this This WP_Theme_Migrator instance.
			 */
			if ( apply_filters( 'wp_theme_migrator_skip_during_ajax', true, $this ) ) {
				return;
			}
		}

		$this->should_migrate = $this->check_migratability();
	}

	/**
	 * Migrate, if we should.
	 */
	protected function maybe_migrate() {
		if ( ! $this->should_migrate ) {
			return;
		}

		$this->maybe_reset_globals();

		$this->switch_theme();

		// Only migrate once in a request.
		$this->stop_migrator();
	}

	/**
	 * Switch to the new theme.
	 */
	protected function switch_theme() {
		// @todo Add child theme support.

		// Filtering the theme-related options early ensures the STYLESHEETPATH
		// and TEMPLATEPATH constants are set.
		add_filter( 'option_stylesheet', [ $this, 'get_theme_stylesheet' ] );
		add_filter( 'option_template', [ $this, 'get_theme_template' ] );
		add_filter( 'option_current_theme', [ $this, 'get_theme_name' ] );
	}

	/**
	 * In WP 6.4.1, the template and stylesheet paths were memoized in globals.
	 * This introduced a bug in which the theme path can not be reset if
	 * `get_template_directory()` or `get_stylesheet_directory()` are called
	 * before the theme has been fully initialized. This method resets the
	 * globals if they are present.
	 *
	 * @see https://github.com/WordPress/wordpress-develop/commit/b6bf3553d975da2e017a00443b7639aacf0a8586
	 */
	protected function maybe_reset_globals(): void {
		if ( ! array_key_exists( 'wp_template_path', $GLOBALS ) && ! array_key_exists( 'wp_stylesheet_path', $GLOBALS ) ) {
			return;
		}

		global $wp_template_path, $wp_stylesheet_path;

		$stylesheet = $this->get_theme_stylesheet();
		$theme_root = get_theme_root( $stylesheet );

		if ( "$theme_root/$stylesheet" !== $wp_stylesheet_path ) {
			$wp_stylesheet_path = null; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		}

		$template   = $this->get_theme_stylesheet();
		$theme_root = get_theme_root( $template );

		if ( "$theme_root/$template" !== $wp_template_path ) {
			$wp_template_path = null; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		}
	}

	/**
	 * Get the theme stylesheet.
	 *
	 * @return string Theme slug.
	 */
	public function get_theme_stylesheet(): string {
		return $this->context->get_theme_slug();
	}

	/**
	 * Get the theme template.
	 *
	 * @return string Theme slug.
	 */
	public function get_theme_template(): string {
		return $this->context->get_theme_slug();
	}

	/**
	 * Get the name of the theme.
	 *
	 * @return string Theme name.
	 */
	public function get_theme_name(): string {
		return $this->context->get_theme_name();
	}

	/**
	 * Check if the current request is migratable.
	 *
	 * @return bool Whether the request is migratable.
	 */
	protected function check_migratability(): bool {
		foreach ( $this->context->get_callbacks() as $callback ) {
			// Callbacks are passed the query vars for the current request.
			// Because we're parsing the request early, the query vars will only
			// include vars added in plugins, not themes.
			if ( true === call_user_func_array( $callback, [ $this->wp->query_vars ] ) ) {

				/**
				 * Runs when a request is migratable.
				 *
				 * @param Controller $this Controller instance.
				 */
				do_action( 'wp_theme_migrator_migrating', $this );
				return true;
			}
		}

		/**
		 * Runs when a request is not migratable.
		 *
		 * @param Controller $this Controller instance.
		 */
		do_action( 'wp_theme_migrator_not_migrating', $this );

		return false;
	}

	/**
	 * Stop migrator.
	 */
	protected function stop_migrator() {
		remove_action( 'setup_theme', [ $this, 'run' ], 8 );
		remove_action( 'registered_post_type', [ $this, 'add_post_type_query_vars' ], 10, 2 );
	}

	/**
	 * Add query vars to WP_Theme_Migrator_WP.
	 *
	 * @see WP_Post_Type::add_rewrite_rules()
	 *
	 * @param string        $post_type Post type.
	 * @param \WP_Post_Type $post_type_object Arguments used to register the post type.
	 */
	public function add_post_type_query_vars( $post_type, $post_type_object ) {
		if ( false !== $post_type_object->query_var && $this->wp && is_post_type_viewable( $post_type_object ) ) {
			$this->wp->add_query_var( $post_type_object->query_var );
		}
	}
}

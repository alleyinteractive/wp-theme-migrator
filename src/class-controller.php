<?php
/**
 * Controller class file
 *
 * @package wp_theme_migrator
 */

namespace Alley\WP\Theme_Migrator;

use Alley\WP\Theme_Migrator\Context;
use Alley\WP\Theme_Migrator\WP as WP_Theme_Migrator_WP;

/**
 * Controller class.
 */
class Controller {
	/**
	 * Context instance.
	 *
	 * @var Context
	 */
	protected $context;

	/**
	 * WP_Theme_Migrator_WP instance.
	 *
	 * @var WP_Theme_Migrator_WP
	 */
	protected $wp;

	/**
	 * Whether to handle the current request with the theme we're migrating to.
	 *
	 * @var bool
	 */
	protected $should_migrate;

	/**
	 * Constructor.
	 *
	 * @param Context $context Context object.
	 */
	public function __construct( Context $context ) {
		$this->context = $context;
		$this->wp      = new WP_Theme_Migrator_WP();
		$this->init();
	}

	/**
	 * Add hooks to kick off migrator and tie in useful query vars.
	 */
	protected function init() {
		// Post types and taxonomies must be registered by this point to be used
		// for determining migratability.
		add_action( 'setup_theme', [ $this, 'run' ], 100 );

		// Add query vars for post types to our WP.
		add_action( 'registered_post_type', [ $this, 'add_post_type_query_vars' ], 10, 2);

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
		/**
		 * Skip migration checks while doing cron.
		 *
		 * @param bool              $skip_during_cron Whether to skip.
		 * @param WP_Theme_Migrator $this This WP_Theme_Migrator instance.
		 */
		$skip_during_cron = apply_filters( 'wp_theme_migrator_skip_during_cron', true, $this );

		if ( $skip_during_cron && defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
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

		$this->switch_theme();

		// Only migrate once in a request.
		$this->stop_migrator();
	}

	/**
	 * Switch to the new theme.
	 */
	public function switch_theme() {
		if ( ! $this->is_theme_valid( $this->context->to_theme ) ) {
			return;
		}
		// @todo Add child theme support.
		// @todo Add sidebar and menu support.

		// Filtering the theme-related options early ensures the STYLESHEETPATH
		// and TEMPLATEPATH constants are set.
		add_filter( 'option_stylesheet', fn() => $this->context->to_theme, 5 );
		add_filter( 'option_template', fn() => $this->context->to_theme, 5 );
		add_filter( 'default_option_current_theme', fn() => wp_get_theme( $this->context->to_theme )?->get( 'Name' ) ?? '' );
		add_filter( 'option_current_theme', fn() => wp_get_theme( $this->context->to_theme )?->get( 'Name' ) ?? '' );
	}

	/**
	 * Validate theme.
	 *
	 * @param string $theme Theme slug.
	 * @return bool Whether theme is valid.
	 */
	protected function is_theme_valid( string $theme ): bool {
		$requirements = validate_theme_requirements( $theme );
		if ( is_wp_error( $requirements ) ) {
			error_log(
				sprintf(
					// translators: %s - Error message.
					esc_html__( 'WP Theme Migrator failed. Invalid theme. %s', 'wp-theme-migrator' ),
					$requirements->get_error_message()
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Check if the current request is migratable.
	 *
	 * @return bool Whether the request is migratable.
	 */
	protected function check_migratability(): bool {
		if ( empty( $this->context->callbacks ) ) {
			return false;
		}

		foreach ( $this->context->callbacks as $callback ) {
			// Callbacks are passed the query vars for the current request as
			// Because we're mocking WP early, the query vars will only include
			// vars added in plugins, not themes.
			if ( true === call_user_func_array( $callback, [ $this->wp->query_vars ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Stop migrator.
	 */
	protected function stop_migrator() {
		remove_action( 'setup_theme', [ $this, 'init' ], 100 );
		remove_action( 'registered_post_type', [ $this, 'add_post_type_query_vars' ], 10, 2);
	}

	/**
	 * Add query vars to WP_Theme_Migrator_WP.
	 *
	 * @see WP_Post_Type::add_rewrite_rules()
	 *
	 * @param string       $post_type Post type.
	 * @param \WP_Post_Type $post_type_object Arguments used to register the post type.
	 */
	public function add_post_type_query_vars( $post_type, $post_type_object ) {
		if ( false !== $post_type_object->query_var && $this->wp && is_post_type_viewable( $post_type_object ) ) {
			$this->wp->add_query_var( $post_type_object->query_var );
		}
	}
}

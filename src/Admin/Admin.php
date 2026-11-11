<?php
/**
 * Admin class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin
 * @since 1.0.0
 */
namespace WikiPress\Admin;

use WikiPress\Includes\Settings\Settings;
use WikiPress\Includes\Functions\Helpers\AjaxHelper;
use WikiPress\Includes\Core\Capabilities;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\LoggerHelper;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Admin\FunctionsSidebar;
use WikiPress\Assets\Assets;
use WikiPress\Admin\Manager\Manager;
use WikiPress\Admin\Manager\Tools\ToolsManager;
use WikiPress\Admin\Manager\Dashboard\DashboardManager;
use WikiPress\Admin\Manager\Settings\SettingsManager;
use WikiPress\Admin\Manager\Tools\Export;
use WikiPress\Admin\Manager\Tools\General;
use WikiPress\Admin\Manager\Tools\Import;
use WikiPress\Admin\Manager\Tools\Reset;
use WikiPress\Admin\Manager\Wiki\WikiManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Admin {
	/**
	 * The DashboardManager instance for managing the dashboard page.
	 *
	 * @var DashboardManager
	 * */
	private DashboardManager $dashboard_manager;
	/**
	 * SettingsManager instance for managing settings-related admin pages.
	 *
	 * @var SettingsManager
	 */
	private SettingsManager $settings_manager;
	/**
	 * ToolsManager instance for managing tools-related admin pages.
	 *
	 * @var ToolsManager
	 */
	private ToolsManager $tools_manager;
	/**
	 * WikiManager instance for managing wiki-related admin pages.
	 *
	 * @var WikiManager
	 */
	private WikiManager $wiki_manager;
	/**
	 * Registry of the admin managers.
	 *
	 * @var array<string, Manager>
	 */
	private array $managers;
	/**
	 * LoaderHelper instance for managing action and filter hooks.
	 *
	 * @var LoaderHelper
	 */
	private LoaderHelper $loader;
	/**
	 * Assets instance for managing admin assets.
	 *
	 * @var Assets
	 */
	private Assets $assets;
	/**
	 * Constructor for the Admin class.
	 *
	 * Initializes the various admin managers and registers their assets.
	 *
	 * @param Assets $assets The Assets instance for managing admin assets.
	 */
	public function __construct( Assets $assets ) {
		$this->managers = array(
			'dashboard' 		=> new DashboardManager(),
			'settings'  		=> new SettingsManager(),
			'wiki-manager'      	=> new WikiManager(),
			'tools'     		=> new ToolsManager(),
		);
		/**
		 * Initialize the individual manager instances from the registry.
		 */
		$this->dashboard_manager = $this->managers['dashboard'];
		/**
		 * Initialize the settings manager instance from the registry.
		 */
		$this->settings_manager  = $this->managers['settings'];
		/**
		 * Initialize the wiki manager instance from the registry.
		 */
		$this->wiki_manager      = $this->managers['wiki-manager'];
		/**
		 * Initialize the tools manager instance from the registry.
		 */
		$this->tools_manager     = $this->managers['tools'];
		/**
		 * Initialize the loader helper.
		 */
		$this->loader = new LoaderHelper();
		new General( $this->loader );
		new Reset( $this->loader );
		new Import( $this->loader );
		new Export( $this->loader );
		/**
		 * Initialize the assets manager.
		 */
		$this->assets = $assets;
		/**
		 * Register assets for the admin managers.
		 */
		foreach ( $this->managers as $manager ) {
			$manager->register_assets( $assets );
		}
		/**
		 * Register assets for the plugin functions manager.
		 */
		$this->loader->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'wp_ajax_wikipress_dismiss_onboarding',
					'callback' => 'dismiss_onboarding',
				),
			)
		);
		$this->loader->run();
	}
	/**
	 * Register admin menu pages and subpages.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_menu(): void {
		LoggerHelper::write_log( 'WikiPress admin menu registration started.' );

		try {
			FunctionsSidebar::register_admin_menu( $this );
			LoggerHelper::write_log( 'WikiPress admin menu registration complete.' );
		} catch ( \Throwable $e ) {
			LoggerHelper::write_log( 'WikiPress admin menu registration failed: ' . $e->getMessage() );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				wp_die( esc_html( $e->getMessage() ), __( 'WikiPress admin menu error', 'wikipress' ), array( 'back_link' => true ) );
			}
		}
	}
	/**
	 * Render the dashboard page.
	 *
	 * This method is responsible for rendering the dashboard page of the WikiPress plugin.
	 * It delegates the rendering to the DashboardManager instance.
	 */
	public function render_dashboard(): void {
		$group = RequestHelper::get_key( 'group', '' );
		$tab   = RequestHelper::get_key( 'tab', '' );
		$this->route_manager( $group, $tab );
	}

	/**
	 * Dispatch the admin request to the correct manager.
	 *
	 * @param string $group Requested group slug.
	 * @param string $tab Requested tab slug.
	 * @return void
	 */
	public function route_manager( string $group, string $tab = '', string $form = '' ): void {
		$group = sanitize_key( $group );
		$tab = sanitize_key( $tab );
		$form = sanitize_key( $form );
		$normalized_group = array(
			'wikipress'       => 'dashboard',
			'dashboard'       => 'dashboard',
			'wiki-manager'    => 'wikis',
			'settings'        => 'settings',
			'tools'           => 'tools',
			'reports'         => 'reports',
		)[ $group ] ?? 'dashboard';

		LoggerHelper::write_log( sprintf( 'WikiPress dashboard render triggered. Group=%s Tab=%s Form=%s', $group, $tab, $form ) );

		try {
			switch ( $normalized_group ) {
				case 'wikis':
					LoggerHelper::write_log( 'WikiPress dashboard routed to wikis page.' );
					$this->render_wikis( $tab );
					return;
				case 'settings':
					LoggerHelper::write_log( 'WikiPress dashboard routed to settings page.' );
					$this->render_settings();
					return;
				case 'tools':
					LoggerHelper::write_log( 'WikiPress dashboard routed to tools page.' );
					$this->render_tools();
					return;
				default:
					LoggerHelper::write_log( 'WikiPress dashboard default render path selected.' );
					$this->dashboard_manager->render();
			}
		} catch ( \Throwable $e ) {
			LoggerHelper::write_log( 'WikiPress dashboard render failed: ' . $e->getMessage() );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				wp_die( esc_html( $e->getMessage() ), __( 'WikiPress dashboard error', 'wikipress' ), array( 'back_link' => true ) );
			}
		}
	}
	/**
	 * Dismiss the onboarding modal.
	 *
	 * This method handles the AJAX request to dismiss the onboarding modal for the WikiPress plugin.
	 */
	public function dismiss_onboarding(): void {
		if ( ! AjaxHelper::authorized( 'wikipress_dismiss_onboarding', 'manage_options' ) ) {
			AjaxHelper::unauthorized( __( 'You are not authorized to dismiss the WikiPress onboarding modal.', 'wikipress' ) );
		}

		Settings::set( 'first_install_complete', true );
		Settings::set( 'onboarding_steps_complete', 3 );

		AjaxHelper::success( array( 'dismissed' => true ) );
	}
	/**
	 * Render WikiPress mods page.
	 *
	 * This method is responsible for rendering the mods page of the WikiPress plugin.
	 * It delegates the rendering to the ModManager instance.
	 */
	public function render_wikis( string $tab = '' ): void {
		LoggerHelper::write_log( 'WikiPress wikis render started.' );
		$this->wiki_manager->render( $tab );
		LoggerHelper::write_log( 'WikiPress wikis render complete.' );
	}
	/**
	 * Render the settings page.
	 *
	 * This method is responsible for rendering the settings page of the WikiPress plugin.
	 * It delegates the rendering to the SettingsManager instance.
	 */
	public function render_settings(): void {
		LoggerHelper::write_log( 'WikiPress settings page render started.' );
		$this->settings_manager->render();
		LoggerHelper::write_log( 'WikiPress settings page render complete.' );
	}
	/**
	 * Render the tools page.
	 *
	 * @return void
	 */
	public function render_tools(): void {
		LoggerHelper::write_log( 'WikiPress tools page render started.' );
		$this->tools_manager->render();
		LoggerHelper::write_log( 'WikiPress tools page render complete.' );
	}
	/**
	 * Get the capability for a given key, with a fallback.
	 *
	 * @param string $key The settings key to retrieve the capability for.
	 * @param string $fallback The fallback capability if the key is not set or invalid.
	 * @return string The capability associated with the key, or the fallback if not valid.
	 */
	public function capability( string $key, string $fallback ): string {
		$value   = Settings::get( $key, $fallback );
		$values  = is_array( $value ) ? $value : array( $value );
		$allowed = array_merge( array( 'manage_options', 'edit_posts', 'publish_posts', 'manage_categories', 'delete_posts' ), array_keys( Capabilities::definitions() ) );
		foreach ( $values as $value ) {
			$capability = SanitizationHelper::key( $value, $fallback );
			if ( in_array( $capability, $allowed, true ) ) {
				return $capability;
			}
		}
		return $fallback;
	}
}



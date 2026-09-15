<?php
/**
 * WikiPress - Elementor WikiPress Plugin
 *
 * @package WikiPress
 * @since 1.0.0
 */
namespace WikiPress\Includes\Plugins\Elementor;

use WikiPress\Includes\Plugins\AssetsProviderInterface;
use WikiPress\Includes\Plugins\I18nProviderInterface;
use WikiPress\Includes\Plugins\PluginInterface;
use WikiPress\Includes\Plugins\SettingsProviderInterface;
use WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use WikiPress\Includes\Plugins\Elementor\Assets\Assets;
use WikiPress\Includes\Plugins\Elementor\Includes\I18n;
use WikiPress\Includes\Plugins\Elementor\Includes\Includes;
use WikiPress\Includes\Plugins\Elementor\Includes\Settings\Settings;
use WikiPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;

final class Elementor implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface {
    /**
     * Singleton instance of the Elementor plugin.
     *
     * @var self|null
     */
    private static ?self $instance = null;
    /**
     * LoaderHelper instance for managing actions and filters.
     *
     * @var LoaderHelper
     */
    private LoaderHelper $loader;
    /**
     * Get the singleton instance of the Elementor plugin.
     *
     * @return self The singleton instance of the Elementor plugin.
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }
    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {
        $this->loader = new LoaderHelper();
        define( 'WIKIPRESS_ELEMENTOR', WIKIPRESS_PLUGINS . '/Elementor' );
        define( 'WIKIPRESS_ELEMENTOR_URL', WIKIPRESS_PLUGINS_URL . '/Elementor' );
    }
    /**
     * Initialize the Elementor plugin.
     *
     * This method checks if the Elementor plugin is available and enabled in the settings.
     * If both conditions are met, it registers the necessary actions and filters for the plugin.
     */
    public function init(): void {
        if ( ! $this->is_available() || ! Settings::enabled() ) {
            return;
        }

        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'elementor/elements/categories_registered', 'callback' => 'register_category' ],
        ] );
        $this->loader->add_action( 'elementor/widgets/register', Widgets::class, 'register' )->run();
    }
    /**
     * Check if the Elementor plugin is available.
     *
     * @return bool True if Elementor is available, false otherwise.
     */
    public function get_slug(): string {
        return 'wikipress-elementor';
    }
    /**
     * Get the name of the Elementor plugin.
     *
     * @return string The name of the plugin.
     */
    public function get_name(): string {
        return 'Elementor';
    }
    /**
     * Get the version of the Elementor plugin.
     *
     * @return string The version of the plugin.
     */
    public function get_version(): string {
        return WIKIPRESS_VERSION;
    }
    /**
     * Get the author of the Elementor plugin.
     *
     * @return string The author of the plugin.
     */
    public function get_author(): string {
        return 'MrTrilB';
    }
    /**
     * Get the author URI of the Elementor plugin.
     *
     * @return string The author URI of the plugin.
     */
    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }
    /**
     * Get the description of the Elementor plugin.
     *
     * @return string The description of the plugin.
     */
    public function get_description(): string {
        return __( 'Provides Elementor integration for WikiPress.', 'wikipress' );
    }
    /**
     * Get the URI of the Elementor plugin.
     *
     * @return string The URI of the plugin.
     */
    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/wikipress';
    }
    /**
     * Get the license of the Elementor plugin.
     *
     * @return string The license of the plugin.
     */
    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }
    /**
     * Check if the Elementor plugin is active.
     *
     * @return bool True if the plugin is active, false otherwise.
     */
    public function is_active(): bool {
        return true;
    }
    /**
     * Register the settings for the Elementor plugin.
     *
     * @return void
     */
    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }
    /**
     * Get the settings page for the Elementor plugin.
     *
     * @return array The settings page configuration.
     */
    public function get_settings_page(): array {
        return Includes::get_instance()->settings()->get_settings_page();
    }
    /**
     * Sanitize the settings input for the Elementor plugin.
     *
     * @param mixed $input The input to sanitize.
     * @return array The sanitized settings.
     */
    public function sanitize_settings( $input ): array {
        return Includes::get_instance()->settings()->sanitize( $input );
    }
    /**
     * Load the text domain for the Elementor plugin.
     *
     * @return void
     */
    public function register_assets(): void {
        ( new Assets() )->register();
    }
    /**
     * Load the text domain for the Elementor plugin.
     *
     * @return void
     */
    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
    /**
     * Check if the Elementor plugin is available.
     *
     * @return bool True if Elementor is available, false otherwise.
     */
    public function is_available(): bool {
        return class_exists( '\\Elementor\\Plugin' ) && class_exists( '\\Elementor\\Widget_Base' );
    }
    /**
     * Register a custom category for Elementor elements.
     *
     * @param \Elementor\Elements_Manager $elements_manager The Elementor elements manager.
     * @return void
     */
    public function register_category( $elements_manager ): void {
        $elements_manager->add_category(
            'trilbdev-wiki',
            [
                'title' => __( 'WikiPress', 'wikipress' ),
                'icon'  => 'eicon-book',
            ]
        );
    }
}

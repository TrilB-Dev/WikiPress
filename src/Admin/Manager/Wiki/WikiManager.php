<?php
/**
 * Base manager for WikiPress mod admin screens.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Wiki
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Assets\Assets;
use WikiPress\Includes\Functions\Helpers\PermissionHelper;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Admin\FunctionsWiki;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\AlertHelper;

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class WikiManager extends Manager {
    /**
     * The Page variable.
     *
     * @since 1.0.0
     * @access protected
     * @var string $page
     */
    protected string $page;

    /**
     * The functions mod handler instance used for admin-post actions.
     *
     * @since 1.0.0
     * @var FunctionsWiki|null
     */
    private ?FunctionsWiki $functions_wiki = null;

    /**
     * Supported mod tabs.
     *
     * @var array<int, string>
     * @since 1.0.0
     */
    protected array $tabs;

    /**
     * Initialize the mod manager.
     *
     * @since 1.0.0
     */
    public function __construct() {
        /**
         * Mod Manager Page
         * Initializes the mod manager page and its tabs.
         * 
         * @since 1.0.0
         */
        $this->page = 'wiki-manager';
        /**
         * Supported mod tabs.
         * 
         * @since 1.0.0
         */
        $this->tabs = array(
            'dashboard',
            'wikis',
            'categories',
            'tags',
        );
        /**
         * Register actions for Mod Manager.
         *
         * @since 1.0.0
         */
        $this->register_actions();
        /**
         * Register the assets for the Mod Manager.
         *
         * When the plugin bootstrap has not yet defined the asset URL constant,
         * we still want unit tests to exercise the REST registration without
         * tripping a fatal asset bootstrap error.
         *
         * @since 1.0.0
         */

        $this->register_assets( new Assets() );
    }
    /**
     * Register the mod manager admin-post handlers used by taxonomy forms.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_actions(): void {
        $this->functions_wiki = $this->functions_wiki ?? new FunctionsWiki();

        //add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        add_action( 'admin_post_wikipress_save_wiki', array( $this->functions_wiki, 'save_wiki' ) );
        add_action( 'admin_post_wikipress_delete_wiki', array( $this->functions_wiki, 'delete_wiki' ) );
        add_action( 'admin_post_wikipress_save_wiki_page', array( $this->functions_wiki, 'save_wiki_page' ) );
        add_action( 'admin_post_wikipress_delete_wiki_page', array( $this->functions_wiki, 'delete_wiki_page' ) );
        add_action( 'admin_post_wikipress_save_tags', array( $this->functions_wiki, 'save_tags' ) );
        add_action( 'admin_post_wikipress_delete_tags', array( $this->functions_wiki, 'delete_tags' ) );
        add_action( 'admin_post_wikipress_save_categories', array( $this->functions_wiki, 'save_categories' ) );
        add_action( 'admin_post_wikipress_delete_categories', array( $this->functions_wiki, 'delete_categories' ) );
    }
    /**
     * Register the assets for the mod manager.
     *
     * @param Assets $assets Assets instance.
     * @return void
     * @since 1.0.0 
     */
    public function register_assets( Assets $assets ): void {
        $this->register_page_assets(
            $assets,
            array( 'wikipress', 'wiki-manager' ),
            'wiki'
        );
    }
    /**
     * Render the mod manager shell.
     *
     * @return void
     * @since 1.0.0
     */
    public function render( string $tab = '', string $form = '' ): void {
        $tab = '' !== $tab ? SanitizationHelper::key( $tab, 'dashboard' ) : SanitizationHelper::key( wp_unslash( $_GET['tab'] ?? 'dashboard' ), 'dashboard' );
        $tab = $this->normalize_tab( $tab );
        $form = $this->normalize_form_state( $form );
        if ( '' === $form ) {
            $form = $this->normalize_form_state( RequestHelper::get_key( 'form', '' ) );
        }
        $capabilities = array(
            'dashboard' => 'wikipress_manager_dashboard',
            'wikis'    => 'wikipress_manager_wiki_view',
            'tags'      => 'wikipress_manager_tags_view',
            'categories' => 'wikipress_manager_categories_view',
        );

        if ( ! PermissionHelper::can( $capabilities[ $tab ] ?? 'wikipress_manager_dashboard' ) ) {
            wp_die( esc_html__( 'You are not authorized to access this WikiPress screen.', 'wikipress' ) );
        }

        $this->header( $this->title( $tab ) );
        $this->render_tab_content( $tab, $form );
        $this->footer();
    }

    /**
     * Render the content for a specific tab.
     *
     * @param string $tab Tab slug.
     * @return void
     * @since 1.0.0
     */
    public function render_tab_content( string $tab, string $form = '' ): void {
        $tab = $this->normalize_tab( $tab );
        $form = $this->normalize_form_state( $form );

        $view_capabilities = [
            'dashboard' => [
                'wikipress_manager_dashboard_view',
            ],
            'wikis' => [
                'wikipress_manager_wikis_view',
            ],
            'categories' => [
                'wikipress_manager_categories_view',
            ],
            'tags' => [
                'wikipress_manager_tags_view',
            ],
        ];

        $can_view = true;
        foreach ( $view_capabilities[ $tab ] ?? [] as $capability ) {
            if ( current_user_can( 'manage_options' ) || current_user_can( $capability ) ) {
                continue;
            }

            $can_view = false;
            break;
        }

        if ( ! $can_view ) {
            wp_die( esc_html__( 'You are not authorized to view these Mod Manager pages.', 'wikipress' ) );
        }

        $screen = $this->get_tab_screen( $tab );
        if ( null === $screen ) {
            return;
        }

        $screen_content = $screen;
        if ( 'mods' === $tab && in_array( $form, array( 'new', 'edit' ), true ) && $screen_content instanceof Mods ) {
            $screen_content->render_page_content( $form );
            return;
        }
        ?>

        <div class="wikipress-wiki-manager-tab-content" role="tabpanel">
            <?php $screen_content->render_page_content(); ?>
        </div>
        <?php
    }

    /**
     * Resolve the page renderer for a specific wiki manager tab.
     *
     * @param string $tab The tab slug.
     * @return object|null
     * @since 1.0.0
     */
    private function get_tab_screen( string $tab ): ?object {
        switch ( $tab ) {
            case 'dashboard':
                return new Dashboard();
            case 'wikis':
                return new Wikis();
            case 'categories':
                return new Categories();
            case 'tags':
                return new Tags();
            default:
                return new Dashboard();
        }
    }

    /**
     * Render the specific page content for a screen class.
     *
     * @return void
     * @since 1.0.0
     */
    public function render_page_content(): void {
    // Intentionally left for concrete screen classes.
    }

    /**
     * Normalize a route alias.
     *
     * @param string $tab Raw tab.
     * @return string
     * @since 1.0.0
     */
    protected function normalize_tab( string $tab ): string {
        $allowed = [ 
            'dashboard', 
            'wikis', 
            'categories', 
            'tags' 
        ];

        if ( in_array( $tab, $allowed, true ) ) {
            return $tab;
        }
        return 'dashboard';
    }

    /**
     * Normalize a managed form state to a supported value.
     *
     * @param string $value Raw form state.
     * @return string
     * @since 1.0.0
     */
    protected function normalize_form_state( string $value ): string {
        $normalized = sanitize_key( $value );
        $allowed    = array(
            'new',
            'edit',
        );

        if ( in_array( $normalized, $allowed, true ) ) {
            return $normalized;
        }

        return '';
    }

    /**
     * Get the page title for a given tab.
     *
     * @param string $tab Tab slug.
     * @return string
     * @since 1.0.0
     */
    protected function title( string $tab ): string {
        $labels = array(
            'dashboard'     => __( 'Wiki Manager - Dashboard', 'wikipress' ),
            'wikis'        => __( 'Wiki Manager - Wikis', 'wikipress' ),
            'categories'     => __( 'Wiki Manager - Categories', 'wikipress' ),
            'tags'          => __( 'Wiki Manager - Tags', 'wikipress' )
        );

        return $labels[ $tab ] ?? $labels['dashboard'];
    }
}

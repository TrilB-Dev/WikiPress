<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin Assets
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\Demo\Assets
 * @since 1.0.0
 */

namespace WikiPress\Includes\Plugins\InternalWiki\Assets;

use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Helpers\ImageHelper;

final class Assets {
    /**
     * Loader helper instance for managing asset registration and enqueueing.
     * @var LoaderHelper
     */
    private LoaderHelper $loader;
    /**
     * Constructor for the Assets class.
     *
     * @param LoaderHelper|null $loader Optional loader helper instance. If not provided, a new instance will be created.
     */
    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    /**
     * Constructor for the Demo plugin assets.
     * @version 1.0.0
     */
    public function register(): void {
        $this->loader->register_component( $this, [
            [ 
                'type' => 'filter',
                'hook' => 'wikipress_frontend_assets',
                'callback' => 'register_frontend_assets'
            ],
            [ 
                'type' => 'action',
                'hook' => 'admin_enqueue_scripts',
                'callback' => 'enqueue_admin_assets',
                'priority' => 20
            ],
        ] )->run();
    }
    /**
     * Registers frontend assets for the plugin.
     *
     * @param array $assets The current assets.
     * @return array The updated assets with the plugin's frontend assets added.
     */
    public function register_frontend_assets( array $assets ): array {
        $assets['scripts'][] = [
            'handle' => 'wikipress-internal-wiki-plugin',
            'src' => WIKIPRESS_URL . 'src/includes/Plugins/InternalWiki/Assets/dist/js/internalwiki.js',
            'in_footer' => true,
        ];

        return $assets;
    }
    /**
     * Enqueues admin assets for the plugin.
     *
     * This method checks if the current admin page is the WikiPress manage page and enqueues the necessary scripts.
     *
     * @return void
     */
    public function enqueue_admin_assets(): void {
        if ( ! in_array( RequestHelper::get_key( 'page' ), [ 'wikipress-manage', 'wikipress-settings' ], true ) ) {
            return;
        }

        wp_enqueue_script(
            'wikipress-internal-wiki-admin',
            WIKIPRESS_URL . 'src/Assets/dist/js/admin.internal-wiki.js',
            [ 'wikipress-bootstrap-select' ],
            WIKIPRESS_VERSION,
            true
        );
    }
    /**
     * Retrieves the URL of an image asset.
     *
     * @param string $file The image file name.
     * @return string The URL of the image asset.
     */
    public function get_image( string $file ): string {

        return ImageHelper::get_image_url( 'wikipress-internal-wiki', $file );
    }
}
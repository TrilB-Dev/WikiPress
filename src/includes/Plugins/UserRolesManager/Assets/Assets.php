<?php
/**
 * User Roles Manager Wiki Plugin Assets
 *
 * @package WikiPress
 * @subpackage Plugins\UserRolesManager\Assets
 * @since 1.0.0
 */

namespace WikiPress\Includes\Plugins\UserRolesManager\Assets;

use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Helpers\ImageHelper;

final class Assets {
    /**
     * The loader helper instance for managing asset registration.
     * @var LoaderHelper
     */
    private LoaderHelper $loader;
    /**
     * Constructor for the User Roles Manager plugin assets.
     *
     * @param LoaderHelper|null $loader Optional loader helper instance. If not provided, a new instance will be created.
     */
    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    /**
     * Constructor for the Demo plugin assets.
     */
    public function register(): void {
        $this->loader->register_component( $this, [
            [
                'type' => 'filter',
                'hook' => 'wikipress_admin_assets',
                'callback' => 'register_admin_assets',
                'accepted_args' => 2
            ],
        ] )->run();
    }
    /**
     * Register the admin assets for the User Roles Manager plugin.
     *
     * @param array $assets The existing assets to be filtered.
     * @param string $context The context in which the assets are being registered.
     * @return array The modified assets array with the User Roles Manager assets added.
     */
    public function register_admin_assets( array $assets, string $context = '' ): array {
        if ( 'wikipress-roles-manager' !== RequestHelper::get_key( 'page' ) ) {
            return $assets;
        }

        $base_url = WIKIPRESS_URL . 'src/includes/Plugins/UserRolesManager/Assets/dist/';
        $assets['styles'][] = [
            'handle' => 'wikipress-user-roles-manager',
            'src' => $base_url . 'css/user-roles-manager.css',
            'deps' => [ 'wikipress-bootstrap' ],
        ];
        $assets['scripts'][] = [
            'handle' => 'wikipress-user-roles-manager',
            'src' => $base_url . 'js/user-roles-manager.js',
            'deps' => [ 'wikipress-bootstrap' ],
            'in_footer' => true,
        ];

        return $assets;
    }
    /**
     * Retrieves the URL of an image asset.
     *
     * @param string $file The image file name.
     * @return string The URL of the image asset.
     */
    public function get_image( string $file ): string {

        return ImageHelper::get_image_url( 'wikipress-user-roles-manager-plugin', $file );
    }
}
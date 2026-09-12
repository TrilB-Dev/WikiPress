<?php

namespace WikiPress\Includes\Plugins\Elementor\Assets;

use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\ImageHelper;

final class Assets {
    private LoaderHelper $loader;

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    public function register(): void {
        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'elementor/frontend/after_enqueue_styles', 'callback' => 'enqueue_styles' ],
            [ 'type' => 'action', 'hook' => 'elementor/frontend/after_register_scripts', 'callback' => 'register_scripts' ],
            [ 'type' => 'action', 'hook' => 'elementor/frontend/after_enqueue_scripts', 'callback' => 'enqueue_scripts' ],
        ] )->run();
    }

    public function enqueue_styles(): void {
        wp_enqueue_style( 'wikipress-elementor', WIKIPRESS_URL . 'src/includes/Plugins/Elementor/Assets/dist/css/wiki.css', [], WIKIPRESS_VERSION );
    }

    public function register_scripts(): void {
        wp_register_script( 'wikipress-elementor', WIKIPRESS_URL . 'src/includes/Plugins/Elementor/Assets/dist/js/wiki.js', [ 'jquery' ], WIKIPRESS_VERSION, true );
    }

    public function enqueue_scripts(): void {
        wp_enqueue_script( 'wikipress-elementor' );
    }
    /**
     * Retrieves the URL of an image asset.
     *
     * @param string $file The image file name.
     * @return string The URL of the image asset.
     */
    public function get_image( string $file ): string {

        return ImageHelper::get_image_url( 'wikipress-elementor', $file );
    }
}
<?php

namespace WikiPress\Includes\Plugins\FontAwesome\Assets;

use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Plugins\FontAwesome\Includes\Settings\Settings as FontAwesomeSettings;
use WikiPress\Includes\Functions\Helpers\ImageHelper;

final class Assets {
    private LoaderHelper $loader;

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    public function register(): void {
        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'admin_enqueue_scripts', 'callback' => 'enqueue_admin_assets' ],
        ] )->run();
    }

    public function enqueue_admin_assets( string $hook_suffix = '' ): void {
        $this->enqueue_fontawesome_vendor_assets( $hook_suffix );
        $this->enqueue_icon_picker();
    }

    private function enqueue_fontawesome_vendor_assets( string $hook_suffix ): void {
        $page = sanitize_key( $_GET['page'] ?? '' );
        if ( false === strpos( $hook_suffix, 'wikipress' ) && 0 !== strpos( $page, 'wikipress' ) ) {
            return;
        }

        $source = FontAwesomeSettings::source();
        $kit_id = FontAwesomeSettings::kit_id();
        $use_kit = '' !== $kit_id;

        if ( $use_kit ) {
            foreach ( [ 'font-awesome-kit', 'font-awesome-cdn' ] as $handle ) {
                wp_dequeue_style( $handle );
                wp_dequeue_script( $handle );
            }
        }

        wp_add_inline_script(
            'wikipress-admin-ui',
            'window.wikipressFontAwesomeSettings = ' . wp_json_encode( [
                'source' => $source,
                'kit_id' => $kit_id,
            ] ) . ';',
            'before'
        );

        if ( $use_kit ) {
            return;
        }

        $handle = 'kit' === $source ? 'font-awesome-kit' : 'font-awesome-cdn';
        if ( wp_style_is( $handle, 'registered' ) || wp_style_is( $handle, 'enqueued' ) ) {
                wp_enqueue_style( $handle );
        }

        if ( wp_script_is( $handle, 'registered' ) || wp_script_is( $handle, 'enqueued' ) ) {
            wp_enqueue_script( $handle );
        }
    }

    public function enqueue_icon_picker(): void {
        if ( ! $this->should_enqueue_icon_picker() ) {
            return;
        }

        wp_enqueue_style(
            'wikipress-fontawesome-icon-picker',
            WIKIPRESS_URL . 'src/includes/Plugins/FontAwesome/Assets/dist/css/icon-picker.css',
            [],
            WIKIPRESS_VERSION
        );
        wp_enqueue_script(
            'wikipress-fontawesome-icon-picker',
            WIKIPRESS_URL . 'src/includes/Plugins/FontAwesome/Assets/dist/js/icon-picker.js',
            [ 'jquery' ],
            WIKIPRESS_VERSION,
            true
        );

        wp_localize_script( 'wikipress-fontawesome-icon-picker', 'wikipress_fa_picker', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'wikipress_fontawesome_picker' ),
            'strings' => [
                'search_placeholder' => __( 'Search icons...', 'wikipress' ),
                'no_icons_found' => __( 'No icons found', 'wikipress' ),
                'loading' => __( 'Loading...', 'wikipress' ),
                'select_icon' => __( 'Select Icon', 'wikipress' ),
                'close' => __( 'Close', 'wikipress' ),
            ],
        ] );
    }

    private function should_enqueue_icon_picker(): bool {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return false;
        }

        return strpos( $screen->id, 'wikipress' ) !== false
            || in_array( $screen->id, [ 'post', 'page', 'custom_css', 'customize' ], true );
    }
    /**
     * Retrieves the URL of an image asset.
     *
     * @param string $file The image file name.
     * @return string The URL of the image asset.
     */
    public function get_image( string $file ): string {

        return ImageHelper::get_image_url( 'wikipress-fontawesome', $file );
    }
}
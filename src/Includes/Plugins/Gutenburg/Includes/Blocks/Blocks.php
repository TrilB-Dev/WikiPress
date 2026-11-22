<?php
/**
 * Register WikiPress Gutenberg blocks.
 *
 * @package WikiPress
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks;

use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\APMenuUser;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Blocks {
    private const BLOCKS = [
        'wikipress-menu-user' => APMenuUser::class,
    ];

    public static function register(): void {
        foreach ( self::BLOCKS as $block => $renderer ) {
            if ( ! Settings::block_enabled( $block ) ) {
                continue;
            }

            $path = __DIR__ . '/' . $block;
            register_block_type(
                $path,
                [ 'render_callback' => [ $renderer, 'render' ] ]
            );
        }
    }
}


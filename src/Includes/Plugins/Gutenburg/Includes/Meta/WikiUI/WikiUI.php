<?php
/**
 * Renderer for the Wiki UI block.
 *
 * @package WikiPress
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes\Meta\WikiUI;

use WikiPress\Includes\Functions\Helpers\PostHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class WikiUI {
    public static function render( array $attributes = [], string $content = '', ?\WP_Block $block = null ): string {
        $post = PostHelper::current();
        if ( ! $post ) {
            return '';
        }

        $label = get_the_title( $post );
        return '';
    }
}



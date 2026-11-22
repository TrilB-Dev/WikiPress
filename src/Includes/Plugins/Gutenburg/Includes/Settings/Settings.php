<?php
/**
 * Settings for the WikiPress Gutenberg blocks.
 *
 * @package WikiPress
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes\Settings;

use WikiPress\Includes\Settings\Settings as BaseSettings;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;

final class Settings {
    public const GROUP = 'gutenburg';

    public function register(): void {
        BaseSettings::register_group( self::GROUP, [
            'gutenburg_enabled' => true,
            'gutenburg_block_wiki_breadcrumbs' => true,
            'gutenburg_block_wiki_list' => true,
            'gutenburg_block_wiki_reading_time' => true,
            'gutenburg_block_wiki_related' => true,
            'gutenburg_block_wiki_toc' => true,
            'gutenburg_block_wiki_search_modal' => true,
        ] );
    }

    public static function enabled(): bool {
        return BaseSettings::get_bool( 'gutenburg_enabled', true );
    }

    public static function block_enabled( string $slug ): bool {
        return self::enabled() && BaseSettings::get_bool( 'gutenburg_block_' . SanitizationHelper::key( $slug ), true );
    }

    public function get_settings_page(): array {
        return [
            'slug' => self::GROUP,
            'label' => __( 'Gutenberg', 'wikipress' ),
            'title' => __( 'Gutenberg integration', 'wikipress' ),
            'layout' => 'box',
            'fields' => [
                [ 'key' => 'gutenburg_enabled', 'label' => __( 'Enable WikiPress Gutenberg blocks', 'wikipress' ), 'description' => __( 'Enable the WikiPress blocks available in the block editor.', 'wikipress' ), 'tooltip' => __( 'Disable this to remove WikiPress blocks from the Gutenberg editor.', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_breadcrumbs', 'label' => __( 'Wiki Breadcrumbs', 'wikipress' ), 'description' => __( 'Show the current wiki location and hierarchy.', 'wikipress' ), 'tooltip' => __( 'Adds breadcrumb navigation to block-based layouts.', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_list', 'label' => __( 'Wiki List', 'wikipress' ), 'description' => __( 'Show a list of WikiPress content.', 'wikipress' ), 'tooltip' => __( 'Use this block to display wiki entries in a page or post.', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_reading_time', 'label' => __( 'Wiki Reading Time', 'wikipress' ), 'description' => __( 'Show the estimated reading time for a wiki page.', 'wikipress' ), 'tooltip' => __( 'The estimate is based on the page content.', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_related', 'label' => __( 'Wiki Related', 'wikipress' ), 'description' => __( 'Show related wiki content.', 'wikipress' ), 'tooltip' => __( 'Related content helps visitors continue exploring the wiki.', 'wikipress' ), 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_toc', 'label' => __( 'Wiki Table of Contents', 'wikipress' ), 'description' => __( 'Show a table of contents for wiki page headings.', 'wikipress' ), 'tooltip' => __( 'The table of contents is generated from headings in the page.', 'wikipress' ), 'tooltip_type' => 'info', 'default' => true ],
                [ 'key' => 'gutenburg_block_wiki_search_modal', 'label' => __( 'Wiki Search Modal', 'wikipress' ), 'description' => __( 'Add a modal search interface for wiki content.', 'wikipress' ), 'tooltip' => __( 'Visitors can open the modal and search without leaving the page.', 'wikipress' ), 'default' => true ],
            ],
        ];
    }

    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];
        foreach ( array_column( $this->get_settings_page()['fields'], 'key' ) as $key ) {
            $input[ $key ] = ! empty( $input[ $key ] );
            BaseSettings::set( $key, $input[ $key ] );
        }
        return $input;
    }
}


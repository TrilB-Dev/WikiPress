<?php
/**
 * Defaults class for defining plugin default values.
 *
 * @package WikiPress
 * @subpackage Includes\Core
 * @since 1.0.0
 */
namespace WikiPress\Includes\Core;

use WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Defaults {
    /**
     * Install all default values for the plugin.
     *
     * @return void
     * @since 1.0.0
     */
	public static function install_defaults(): void {
		foreach ( self::default_settings() as $group => $values ) {
			if ( is_array( $values ) ) {
				Settings::set_group( $group, $values );
			}
		}
	}
    /**
     * Get the plugin default settings.
     *
     * @return array<string, array<string, mixed>>
     * @since 1.0.0
     */
    public static function default_settings(): array {
        return [
            'setup' => [
                'first_install_complete' => false,
                'onboarding_steps_complete' => 0,
            ],
            'general' => [
                'root_name' => 'WikiPress',
                'root_description' => '',
                'archive_title' => '',
                'archive_description' => '',
                'root_slug' => 'catalogue',
                'category_slug' => '',
                'tag_slug' => '',
                'permalink' => '%root%/%wiki_parent%/%wiki_category%/%wiki_tag%/%wiki_page%',
                'enable_schema' => true,
            ],
            'layout' => [
                'show_search' => true,
                'show_toc' => true,
                'show_breadcrumbs' => true,
                'show_last_updated' => true,
                'show_author' => false,
                'show_reading_time' => false,
                'show_feedback' => true,
                'show_related_pages' => true,
                'related_pages_count' => 4,
                'search_placeholder' => __( 'Search the catalogue', 'wikipress' ),
                'search_button_text' => __( 'Search', 'wikipress' ),
                'search_scope' => 'all',
                'search_no_results_message' => __( 'No catalogue entries found.', 'wikipress' ),
                'search_results_count' => 10,
                'search_min_chars' => 2,
                'search_live_results' => true,
                'show_sidebar' => true,
                'sidebar_position' => 'left',
                'sidebar_width' => 280,
                'sidebar_sticky' => true,
                'sidebar_show_categories' => true,
                'sidebar_show_category_count' => false,
                'sidebar_expand_categories' => true,
                'sidebar_show_page_count' => false,
                'page_show_title' => true,
                'page_show_toc' => true,
                'page_toc_position' => 'sidebar',
                'toc_min_level' => 2,
                'toc_max_level' => 4,
                'page_show_navigation' => true,
                'reading_time_wpm' => 200,
            ],
            'access' => [],
            'tools' => [
                'debug_logging' => false,
                'console_logging' => false,
            ],
        ];
    }
}


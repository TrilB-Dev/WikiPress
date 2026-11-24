<?php
/**
 * Menus class for registering plugin menus.
 *
 * @package WikiPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace WikiPress\Includes\Pages;

use WikiPress\Includes\Core\Menus as CoreMenus;
use WikiPress\Includes\Functions\Helpers\MenuHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Menus {
	/**
	 * Register all page-layer menu locations.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::primary_navigation();
		add_filter( 'wp_nav_menu_objects', array( __CLASS__, 'filter_wiki_menu_labels' ), 10, 2 );
		add_filter( 'the_title', array( __CLASS__, 'filter_menu_meta_box_page_title' ), 10, 2 );
	}

	/**
	 * Register the built-in primary navigation menu location.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function primary_navigation(): bool {
		return CoreMenus::get_instance()->register_location( 
            'primary', 
            __( 'Primary Navigation', 'wikipress' ), 
            true 
        ) instanceof CoreMenus;
	}

	/**
	 * Prefix WikiPress page labels in rendered menu items.
	 *
	 * @param array<int, \WP_Post> $items Menu item objects.
	 * @param \stdClass            $args Menu arguments.
	 * @return array<int, \WP_Post>
	 */
	public static function filter_wiki_menu_labels( array $items, \stdClass $args ): array {
		foreach ( $items as $item ) {
			if ( 'wikipress_wiki_page' === (string) $item->object ) {
				$item->title = MenuHelper::prefix_page_title( (string) $item->title, (int) $item->object_id );
			}
		}
		return $items;
	}

	/**
	 * Prefix wiki-page titles in WordPress nav-menu meta boxes.
	 *
	 * @param string $title Post title.
	 * @param int    $post_id Post ID.
	 * @return string
	 */
	public static function filter_menu_meta_box_page_title( string $title, int $post_id ): string {
		if ( 'wikipress_wiki_page' !== get_post_type( $post_id ) ) {
			return $title;
		}

		return MenuHelper::prefix_page_title( $title, $post_id );
	}
}


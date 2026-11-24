<?php
/**
 * Helper wrapper around WikiPress core menu registration.
 *
 * @package WikiPress\Includes\Functions\Helpers
 */
namespace WikiPress\Includes\Functions\Helpers;

use WikiPress\Includes\Core\Menus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MenuHelper {
	/**
	 * Core menu registry instance.
	 *
	 * @var Menus
	 */
	private Menus $menus;

	public function __construct( ?Menus $menus = null ) {
		$this->menus = $menus ?? new Menus();
	}

	/**
	 * Register a menu location and return the helper instance.
	 *
	 * @param string $location Menu location slug.
	 * @param string $label Menu location label.
	 * @return self
	 */
	public static function register_location( string $location, string $label = '' ): self {
		$helper = new self();
		$helper->menus->register_location( $location, $label );
		return $helper;
	}

	/**
	 * Get all registered menu locations.
	 *
	 * @return array<string, string>
	 */
	public function get_locations(): array {
		return $this->menus->get_locations();
	}

	/**
	 * Normalize a wiki menu name/location.
	 *
	 * @param string $name Wiki name.
	 * @param string $slug Wiki slug.
	 * @return array{location:string,label:string}
	 */
	public static function wiki_menu( string $name, string $slug ): array {
		$location = 'wiki-' . sanitize_key( $slug );
		return array(
			'location' => $location,
			'label'    => '' !== trim( $name ) ? sprintf( __( '%s Wiki Navigation', 'wikipress' ), trim( $name ) ) : __( 'Wiki Navigation', 'wikipress' ),
		);
	}

	/**
	 * Create or update a wiki navigation location and menu.
	 *
	 * @param int    $wiki_id Wiki post ID.
	 * @param string $name Wiki name.
	 * @param string $slug Wiki slug.
	 * @return int Menu ID.
	 */
	public static function ensure_wiki_menu( int $wiki_id, string $name, string $slug ): int {
		if ( $wiki_id <= 0 || '' === sanitize_key( $slug ) ) {
			return 0;
		}

		$definition = self::wiki_menu( $name, $slug );
		register_nav_menu( $definition['location'], $definition['label'] );

		$menu_name = trim( $name ) . ' Wiki';
		$menu = wp_get_nav_menu_object( $menu_name );
		$menu_id = $menu ? (int) $menu->term_id : 0;

		if ( $menu_id <= 0 ) {
			$created = wp_create_nav_menu( wp_slash( $menu_name ) );
			if ( is_wp_error( $created ) ) {
				return 0;
			}
			$menu_id = absint( $created );
		} else {
			wp_update_nav_menu_object(
				$menu_id,
				array( 'menu-name' => wp_slash( $menu_name ) )
			);
		}

		update_post_meta( $wiki_id, '_wikipress_nav_menu_location', $definition['location'] );
		update_post_meta( $wiki_id, '_wikipress_nav_menu_id', $menu_id );

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations = is_array( $locations ) ? $locations : array();
		$locations[ $definition['location'] ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		self::sync_wiki_page_items( $wiki_id, $menu_id );
		return $menu_id;
	}

	/**
	 * Synchronize wiki pages to the wiki menu.
	 *
	 * @param int $wiki_id Wiki post ID.
	 * @param int $menu_id Menu ID.
	 * @return void
	 */
	public static function sync_wiki_page_items( int $wiki_id, int $menu_id = 0 ): void {
		if ( $wiki_id <= 0 ) {
			return;
		}

		$menu_id = $menu_id > 0 ? $menu_id : absint( get_post_meta( $wiki_id, '_wikipress_nav_menu_id', true ) );
		if ( $menu_id <= 0 ) {
			return;
		}

		$items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );
		$known_page_ids = array();
		foreach ( (array) $items as $item ) {
			if ( 'post_type' === $item->type && 'wikipress_wiki_page' === $item->object ) {
				$known_page_ids[ (int) $item->object_id ] = (int) $item->ID;
			}
		}

		$page_ids = get_posts(
			array(
				'post_type'      => 'wikipress_wiki_page',
				'post_status'    => 'publish',
				'post_parent'    => $wiki_id,
				'fields'         => 'ids',
				'posts_per_page' => -1,
			)
		);

		foreach ( $page_ids as $page_id ) {
			$page_id = absint( $page_id );
			if ( isset( $known_page_ids[ $page_id ] ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					$known_page_ids[ $page_id ],
					array(
						'menu-item-title'     => get_the_title( $page_id ),
						'menu-item-object-id' => $page_id,
						'menu-item-object'    => 'wikipress_wiki_page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
				);
				unset( $known_page_ids[ $page_id ] );
				continue;
			}

			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => get_the_title( $page_id ),
					'menu-item-object-id' => $page_id,
					'menu-item-object'    => 'wikipress_wiki_page',
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		foreach ( $known_page_ids as $stale_menu_item_id ) {
			wp_delete_post( $stale_menu_item_id, true );
		}
	}

	/**
	 * Prefix wiki-page labels in menu/meta controls with the parent wiki name.
	 *
	 * @param string $title Menu/page title.
	 * @param int    $page_id Wiki page ID.
	 * @return string
	 */
	public static function prefix_page_title( string $title, int $page_id ): string {
		$parent_id = absint( get_post_field( 'post_parent', $page_id ) );
		if ( $parent_id <= 0 ) {
			return $title;
		}

		$parent_name = get_the_title( $parent_id );
		return '' !== $parent_name ? $parent_name . ' -- ' . $title : $title;
	}
}



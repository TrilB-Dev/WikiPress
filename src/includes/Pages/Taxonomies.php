<?php
/**
 * Taxonomies class for registering plugin taxonomies.
 *
 * @package WikiPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace WikiPress\Includes\Pages;

use WikiPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Taxonomies {
	/**
	 * Register the page-layer taxonomies.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::category_taxonomy();
		self::tag_taxonomy();
	}

	/**
	 * Register the mod group taxonomy.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function category_taxonomy(): bool {
		if ( taxonomy_exists( 'wikipress_category' ) ) {
			return false;
		}

		return Taxonomy::create(
			'wikipress_category',
			array(
				'label'             => __( 'Categories', 'wikipress' ),
				'description'       => __( 'Categories used to organize mods.', 'wikipress' ),
				'post_type'       	=> array( 'wikipress_posttype' ),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'category' ),
			)
		);
	}

	/**
	 * Register the mod tag taxonomy.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function tag_taxonomy(): bool {
		if ( taxonomy_exists( 'wikipress_tag' ) ) {
			return false;
		}

		return Taxonomy::create(
			'wikipress_tag',
			array(
				'label'             => __( 'Tags', 'wikipress' ),
				'description'       => __( 'Tags used to describe mods.', 'wikipress' ),
				'post_type'       => array( 'wikipress_posttype' ),
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'tag' ),
			)
		);
	}
}

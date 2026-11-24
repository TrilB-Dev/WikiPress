<?php
/**
 * PostTypes class for registering plugin post types.
 *
 * @package WikiPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace WikiPress\Includes\Pages;

use WikiPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PostTypes {
	/**
	 * Register all page-layer post types.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::wiki_posttype();
		self::wiki_page_posttype();
	}

	/**
	 * Register the core WikiPress mod post type.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function wiki_posttype(): bool {
		return PostType::create(
			'wikipress_wiki',
			array(
				'label'           	=> __( 'Wikis', 'wikipress' ),
				'description'     	=> __( 'Wiki managed by WikiPress.', 'wikipress' ),
				'supports'       	=> array( 'title', 'excerpt', 'thumbnail', 'author', 'revisions', 'custom-fields' ),
				'taxonomies'      	=> array( 'wikipress_category', 'wikipress_tag' ),
				'public'          	=> true,
				'show_ui'         	=> true,
				'show_in_menu'    	=> false,
				'menu_position'   	=> 20,
				'menu_icon'       	=> 'dashicons-archive',
				'has_archive'     	=> true,
				'show_in_rest'    	=> true,
				'show_in_nav_menus' => true,
				'rewrite'         	=> array( 'slug' => 'wiki' ),
				'capability_type' 	=> 'post',
				'map_meta_cap'    	=> true,
			)
		);
	}
	/**
	 * Register the core WikiPress wiki page post type.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function wiki_page_posttype(): bool {
		return PostType::create(
			'wikipress_wiki_page',
			array(
				'label'           	=> __( 'Wiki Page', 'wikipress' ),
				'description'     	=> __( 'Wiki page manager for WikiPress.', 'wikipress' ),
				'supports'        	=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'trackbacks', 'page-attributes', 'post-formats', 'custom-fields', 'comments' ),
				'taxonomies'      	=> array( 'wikipress_page_category', 'wikipress_page_tag' ),
				'public'           	=> true,
				'show_ui'          	=> true,
				'show_in_menu'     	=> false,
				'menu_position'    	=> 20,
				'menu_icon'        	=> 'dashicons-archive',
				'has_archive'      	=> true,
				'show_in_rest'     	=> true,
				'show_in_nav_menus' => true,
				'rewrite'          	=> array( 'slug' => 'page' ),
				'capability_type'  	=> 'post',
				'map_meta_cap'     	=> true,
			)
		);
	}
}


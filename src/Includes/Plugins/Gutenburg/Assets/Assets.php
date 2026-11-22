<?php
/**
 * Gutenberg assets for the WikiPress Gutenberg plugin.
 *
 * @package WikiPress
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Assets;

use WikiPress\Includes\Functions\Helpers\LoaderHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Assets {
	public const SCRIPT_HANDLE = 'wikipress-gutenburg-wiki-page-meta';

	private LoaderHelper $loader;

	public function __construct( ?LoaderHelper $loader = null ) {
		$this->loader = $loader ?? new LoaderHelper();
	}

	public function register(): void {
		$this->loader->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'enqueue_block_editor_assets',
					'callback' => 'enqueue_editor_assets',
				),
			)
		)->run();
	}

	public function enqueue_editor_assets(): void {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'wikipress_wiki_page' !== $screen->post_type ) {
			return;
		}

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		$post = $post_id > 0 ? get_post( $post_id ) : null;

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			WIKIPRESS_PLUGINS_URL . '/Gutenburg/Assets/dist/js/admin.gutenburg.js',
			array(
				'wp-plugins',
				'wp-edit-post',
				'wp-editor',
				'wp-components',
				'wp-element',
				'wp-data',
				'wp-core-data',
				'wp-i18n',
			),
			WIKIPRESS_VERSION,
			true
		);

		$wikis = get_posts(
			array(
				'post_type'      => 'wikipress_wiki',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$wiki_options = array();
		foreach ( $wikis as $wiki ) {
			$wiki_options[] = array(
				'label' => $wiki->post_title,
				'value' => (string) $wiki->ID,
			);
		}

		wp_localize_script(
			self::SCRIPT_HANDLE,
			'wikipressGutenbergWikiPage',
			array(
				'wikis'   => $wiki_options,
				'metaKey' => \WikiPress\Includes\Functions\Helpers\PermalinkHelper::OVERRIDE_META,
				'parentId' => $post instanceof \WP_Post ? (int) $post->post_parent : 0,
			)
		);
	}
}

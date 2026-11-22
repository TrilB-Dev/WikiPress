<?php
/**
 * Gutenberg metadata registration for WikiPress wiki pages.
 *
 * @package WikiPress
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes\Meta;

use WikiPress\Includes\Functions\Helpers\PermalinkHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Meta {
	/**
	 * Register Gutenberg metadata and REST API hooks.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		add_action( 'init', array( self::class, 'register_post_meta' ) );
		add_filter( 'rest_prepare_wikipress_wiki_page', array( self::class, 'prepare_editor_link' ), 10, 3 );
	}

	/**
	 * Point Gutenberg's View link at the parent wiki's Wiki Pages screen.
	 *
	 * @param \WP_REST_Response $response REST response.
	 * @param \WP_Post           $post     Wiki page post.
	 * @param \WP_REST_Request  $request  REST request.
	 * @return \WP_REST_Response
	 */
	public static function prepare_editor_link( \WP_REST_Response $response, \WP_Post $post, \WP_REST_Request $request ): \WP_REST_Response {
		if ( $post->post_parent > 0 ) {
			$response->data['link'] = add_query_arg(
				array(
					'page'  => 'wikipress',
					'group' => 'wiki-manager',
					'tab'   => 'wiki-pages',
					'wiki'  => absint( $post->post_parent ),
				),
				admin_url( 'admin.php' )
			);
		}

		return $response;
	}
	/**
	 * Register post meta for WikiPress wiki pages.
	 *
	 * @param string $value The permalink pattern to register.
	 * @return void
	 * @since 1.0.0
	 */
	public static function register_post_meta(): void {
		register_post_meta(
			'wikipress_wiki_page',
			PermalinkHelper::OVERRIDE_META,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( self::class, 'sanitize_permalink' ),
				'auth_callback'     => static function (): bool {
					return current_user_can( 'edit_posts') || current_user_can( 'manage_options' );
				},
			)
		);
	}
	/**
	 * Sanitize permalink pattern for WikiPress wiki pages.
	 *
	 * @param mixed $value The value to sanitize.
	 * @return string
	 * @since 1.0.0
	 */
	public static function sanitize_permalink( $value ): string {
		return PermalinkHelper::sanitize_pattern( is_scalar( $value ) ? (string) $value : '' );
	}
}
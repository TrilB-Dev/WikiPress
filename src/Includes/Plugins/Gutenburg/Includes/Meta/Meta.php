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
	public static function register(): void {
		add_action( 'init', array( self::class, 'register_post_meta' ) );
	}

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

	public static function sanitize_permalink( $value ): string {
		return PermalinkHelper::sanitize_pattern( is_scalar( $value ) ? (string) $value : '' );
	}
}

<?php
/**
 * DataTransfer class for exporting and importing plugin data.
 *
 * @package WikiPress\Includes\Tools
 */
namespace WikiPress\Includes\Tools;

use WikiPress\Includes\Core\PostType;
use WikiPress\Includes\Core\Taxonomy;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\PostHelper;
use WikiPress\Includes\Functions\Helpers\QueryHelper;
use WikiPress\Includes\Functions\Helpers\TaxonomyHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DataTransfer {
	/**
	 * The current version of the export format.
	 *
	 * @since 1.0.0
	 */
	public const VERSION = 1;
	/**
	 * Export all plugin data as an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @return array The exported plugin data.
	 */
	public static function export(): array {
		$data  = array(
			'version'    => self::VERSION,
			'wikis'      => array(),
			'pages'      => array(),
			'categories' => array(),
			'tags'       => array(),
		);
		$query = QueryHelper::posts(
			array(
				'post_type'      => array( PostType::WIKI, PostType::PAGE ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			)
		);
		while ( $query->have_posts() ) {
			$query->the_post();
			$post = PostHelper::current();
			if ( ! $post ) {
				continue;
			}

			$item = array(
				'id'         => $post->ID,
				'title'      => $post->post_title,
				'content'    => $post->post_content,
				'excerpt'    => $post->post_excerpt,
				'status'     => $post->post_status,
				'wiki_id'    => $post->post_parent > 0 ? (int) $post->post_parent : 0,
				'categories' => TaxonomyHelper::names( TaxonomyHelper::terms( Taxonomy::CATEGORY, $post->ID ) ),
				'tags'       => TaxonomyHelper::names( TaxonomyHelper::terms( Taxonomy::TAG, $post->ID ) ),
			);
			$data[ $post->post_type === PostType::WIKI ? 'wikis' : 'pages' ][] = $item;
		}
		wp_reset_postdata();
		foreach ( array(
			'categories' => Taxonomy::CATEGORY,
			'tags'       => Taxonomy::TAG,
		) as $key => $taxonomy ) {
			$terms = TaxonomyHelper::terms( $taxonomy );
			foreach ( $terms as $term ) {
				$data[ $key ][] = array(
					'name'        => $term->name,
					'slug'        => $term->slug,
					'description' => $term->description,
				);
			}
		}
		return $data;
	}
	/**
	 * Export all plugin data as a JSON string.
	 *
	 * @since 1.0.0
	 *
	 * @param int $flags Optional. JSON encode flags. Default 0.
	 * @return string The exported plugin data as a JSON string.
	 */
	public static function export_json( int $flags = 0 ): string {
		$json = wp_json_encode( self::export(), $flags );
		return is_string( $json ) ? $json : '';
	}
	/**
	 * Validate the imported plugin data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data The data to validate.
	 * @return array An array containing the validation result and any errors.
	 */
	public static function validate( $data ): array {
		$errors = array();
		if ( ! is_array( $data ) ) {
			return array(
				'valid'  => false,
				'errors' => array( __( 'The import data must be an object.', 'wikipress' ) ),
			);
		}
		if ( absint( $data['version'] ?? 0 ) !== self::VERSION ) {
			$errors[] = __( 'This WikiPress export version is not supported.', 'wikipress' );
		}
		foreach ( array( 'wikis', 'pages', 'categories', 'tags' ) as $key ) {
			if ( isset( $data[ $key ] ) && ! is_array( $data[ $key ] ) ) {
				/* translators: %s is the name of the export section. */
				$errors[] = sprintf( esc_html__( 'The %s export section must be an array.', 'wikipress' ), $key );
			}
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
		);
	}

	/**
	 * Import plugin data from an associative array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data The data to import.
	 * @return array|\WP_Error An array containing the import results or a WP_Error on failure.
	 */
	public static function import( array $data ): array|\WP_Error {
		$validation = self::validate( $data );
		if ( ! $validation['valid'] ) {
			return new \WP_Error( 'invalid_import', implode( ' ', $validation['errors'] ) );
		}
		$wiki_map = array();
		$result   = array(
			'wikis'      => 0,
			'pages'      => 0,
			'categories' => 0,
			'tags'       => 0,
			'errors'     => array(),
		);
		foreach ( (array) ( $data['wikis'] ?? array() ) as $wiki ) {
			if ( ! is_array( $wiki ) ) {
				$result['errors'][] = __( 'A Wiki entry was skipped because it was invalid.', 'wikipress' );
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => PostType::WIKI,
					'post_title'   => SanitizationHelper::text( $wiki['title'] ?? '' ),
					'post_content' => self::content( $wiki['content'] ?? '' ),
					'post_status'  => self::status( $wiki['status'] ?? 'draft' ),
				),
				true
			);
			if ( ! is_wp_error( $id ) ) {
				$wiki_map[ absint( $wiki['id'] ?? 0 ) ] = (int) $id;
				++$result['wikis'];
			} else {
				$result['errors'][] = $id->get_error_message();
			}
		}
		foreach ( (array) ( $data['pages'] ?? array() ) as $page ) {
			if ( ! is_array( $page ) ) {
				$result['errors'][] = __( 'A page entry was skipped because it was invalid.', 'wikipress' );
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => PostType::PAGE,
					'post_title'   => SanitizationHelper::text( $page['title'] ?? '' ),
					'post_content' => self::content( $page['content'] ?? '' ),
					'post_excerpt' => SanitizationHelper::text( $page['excerpt'] ?? '' ),
					'post_status'  => self::status( $page['status'] ?? 'draft' ),
				),
				true
			);
			if ( ! is_wp_error( $id ) ) {
				++$result['pages'];
				$wiki_parent = $wiki_map[ absint( $page['wiki_id'] ?? 0 ) ] ?? 0;
				if ( $wiki_parent > 0 ) {
					wp_update_post(
						array(
							'ID'          => (int) $id,
							'post_parent' => (int) $wiki_parent,
						)
					);
				}
				self::set_terms( (int) $id, $page['categories'] ?? array(), Taxonomy::CATEGORY );
				self::set_terms( (int) $id, $page['tags'] ?? array(), Taxonomy::TAG );
			} else {
				$result['errors'][] = $id->get_error_message();
			}
		}
		$result['categories'] = self::import_terms( (array) ( $data['categories'] ?? array() ), Taxonomy::CATEGORY );
		$result['tags']       = self::import_terms( (array) ( $data['tags'] ?? array() ), Taxonomy::TAG );
		return $result;
	}
	/**
	 * Import plugin data from a JSON string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $json The JSON string containing the data to import.
	 * @return array|\WP_Error An array containing the import results or a WP_Error on failure.
	 */
	private static function import_terms( array $terms, string $taxonomy ): int {
		$count = 0;
		foreach ( $terms as $term ) {
			if ( ! is_array( $term ) || empty( $term['name'] ) ) {
				continue;
			}
			$inserted = wp_insert_term(
				SanitizationHelper::text( $term['name'] ),
				$taxonomy,
				array(
					'slug'        => SanitizationHelper::slug( $term['slug'] ?? '' ),
					'description' => SanitizationHelper::textarea( $term['description'] ?? '' ),
				)
			);
			if ( ! is_wp_error( $inserted ) ) {
				++$count;
			}
		}
		return $count;
	}
	/**
	 * Set the terms for a specific post.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id The ID of the post.
	 * @param array  $terms The terms to set for the post.
	 * @param string $taxonomy The taxonomy to which the terms belong.
	 */
	private static function set_terms( int $post_id, $terms, string $taxonomy ): void {
		wp_set_post_terms( $post_id, TaxonomyHelper::names( $terms ), $taxonomy, false );
	}
	/**
	 * Sanitize and return a valid post status.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $status The status to sanitize.
	 * @return string The sanitized post status.
	 */
	private static function status( $status ): string {
		if ( ! is_scalar( $status ) ) {
			return 'draft';
		}

		$status = sanitize_key( (string) $status );
		return in_array( $status, array( 'publish', 'draft', 'private' ), true ) ? $status : 'draft';
	}
	/**
	 * Sanitize and return the post content.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $content The content to sanitize.
	 * @return string The sanitized post content.
	 */
	private static function content( $content ): string {
		return is_scalar( $content ) ? wp_kses_post( (string) $content ) : '';
	}
}




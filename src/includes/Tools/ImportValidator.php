<?php
/**
 * Validation helpers for WikiPress import and tool payloads.
 *
 * @package WikiPress
 * @subpackage Includes\Tools
 * @since 1.0.0
 */

namespace WikiPress\Includes\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ImportValidator {
	/**
	 * Validate and parse import data from a JSON string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $json The JSON string containing the import data.
	 * @return array|\WP_Error The parsed data array if valid, or a WP_Error on failure.
	 */
	public static function from_json( string $json ): array|\WP_Error {
		if ( '' === trim( $json ) ) {
			return new \WP_Error( 'empty_import', __( 'The import file is empty.', 'wikipress' ) );
		}

		$data = json_decode( $json, true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_json', __( 'The import file contains invalid JSON.', 'wikipress' ) );
		}

		$validation = DataTransfer::validate( $data );
		return $validation['valid'] ? $data : new \WP_Error( 'invalid_import', implode( ' ', $validation['errors'] ) );
	}
	/**
	 * Validate and parse import data from an uploaded file.
	 *
	 * @since 1.0.0
	 *
	 * @param array $file The uploaded file array (e.g., from $_FILES).
	 * @return array|\WP_Error The parsed data array if valid, or a WP_Error on failure.
	 */
	public static function file( array $file ): array|\WP_Error {
		if ( ! empty( $file['error'] ) ) {
			return new \WP_Error( 'upload_error', __( 'The import file could not be uploaded.', 'wikipress' ) );
		}
		if ( empty( $file['tmp_name'] ) || ! is_readable( $file['tmp_name'] ) ) {
			return new \WP_Error( 'missing_file', __( 'The import file could not be read.', 'wikipress' ) );
		}

		$json = file_get_contents( $file['tmp_name'] );
		return false === $json ? new \WP_Error( 'read_error', __( 'The import file could not be read.', 'wikipress' ) ) : self::from_json( $json );
	}
}




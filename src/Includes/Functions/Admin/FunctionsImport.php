<?php
/**
 * Import-related admin functions for WikiPress.
 *
 * @package WikiPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace WikiPress\Includes\Functions\Admin;

use WikiPress\Includes\Core\Defaults;
use WikiPress\Includes\Tools\DataTransfer;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FunctionsImport {

    /**
     * Import WikiPress data from an uploaded JSON file.
     *
     * @return void
     */
    public function import_data(): void {
        if ( ! \current_user_can( 'wikipress_tools_import' ) ) {
            \wp_die( \esc_html__( 'You are not allowed to import WikiPress data.', 'wikipress' ), 403 );
        }
        \check_admin_referer( 'wikipress_import' );
        $file = $_FILES['wikipress_import_file'] ?? array();
        if ( empty( $file['tmp_name'] ) || ( $file['error'] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK ) {
            \wp_die( \esc_html__( 'Please upload a valid WikiPress JSON export.', 'wikipress' ), 400 );
        }
        $data = json_decode( file_get_contents( $file['tmp_name'] ), true );
        if ( ! is_array( $data ) ) {
            \wp_die( \esc_html__( 'The uploaded file is not valid JSON.', 'wikipress' ), 400 );
        }
        $result = DataTransfer::import( $data );
        if ( is_wp_error( $result ) ) {
            \wp_die( \esc_html( $result->get_error_message() ), 400 );
        }
        \wp_safe_redirect( \admin_url( 'admin.php?page=wikipress&group=tools&tab=import&imported=1' ) );
        exit;
    }

    public function import_group_defaults(): void {
        if ( ! \current_user_can( 'wikipress_tools_import' ) ) {
            \wp_die( \esc_html__( 'You are not allowed to import the default WikiPress groups.', 'wikipress' ), 403 );
        }

        \check_admin_referer( 'wikipress_import_group_defaults', 'wikipress_import_group_defaults_nonce' );

        if ( empty( $_POST['confirm_default_reset'] ) || '1' !== (string) $_POST['confirm_default_reset'] ) {
            \wp_safe_redirect( \admin_url( 'admin.php?page=wikipress&group=tools&tab=import&default_groups_import_failed=1' ) );
            exit;
        }

        $success = Defaults::reset_group_defaults();
        \wp_safe_redirect( \admin_url( 'admin.php?page=wikipress&group=tools&tab=import&default_groups_imported=' . ( $success ? '1' : '0' ) ) );
        exit;
    }
}


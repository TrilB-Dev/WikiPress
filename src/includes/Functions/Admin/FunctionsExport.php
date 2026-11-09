<?php
/**
 * Export-related admin functions for WikiPress.
 *
 * @package WikiPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace WikiPress\Includes\Functions\Admin;

use WikiPress\Includes\Tools\DataTransfer;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FunctionsExport {

    /**
     * Export WikiPress data as a JSON file.
     *
     * @return void
     */
    public function export_data(): void {
        if ( ! current_user_can( 'wikipress_tools_export' ) ) {
            wp_die( esc_html__( 'You are not allowed to export WikiPress data.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_export' );
        nocache_headers();
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=wikipress-export-' . gmdate( 'Y-m-d' ) . '.json' );
        echo wp_json_encode( DataTransfer::export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        exit;
    }
}


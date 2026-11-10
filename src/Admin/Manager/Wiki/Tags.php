<?php
/**
 * Tags page renderer for the WikiPress admin.
 *
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Admin\Manager\Wiki\WikiManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Tags extends WikiManager {

	/**
     * Renders the content for the Wiki Tags page.
     *
     * @since 1.0.0
     */
	public function render_page_content(): void {
        // Render the content for the Wiki Tags page here.
        ?>
        <div class="card shadow-sm border-0">
			<div class="card-body p-4">
            </div>
        </div>
        <?php
    }
	/**
     * Renders the modal for creating or editing a tag.
     *
     * @since 1.0.0
     */
	public function render_tag_modal(): void {
        // Render the modal for creating or editing a tag here.
        ?>
        <div class="modal fade" id="tagModal" tabindex="-1" aria-labelledby="tagModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tagModalLabel"><?php esc_html_e( 'Tag', 'wikipress' ); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Modal content goes here -->
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
<?php
/**
 * Wikis page renderer for the WikiPress admin.
 *
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Admin\Manager\Wiki\WikiManager;
use WikiPress\Includes\Functions\Helpers\AlertHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Wikis extends WikiManager {
	/**
     * Renders the content for the Wiki Wikis page.
     *
     * @since 1.0.0
     */
    public function render_page_content(): void {
        $wiki_notice = isset( $_GET['wikipress_notice'] ) ? sanitize_key( wp_unslash( $_GET['wikipress_notice'] ) ) : '';
        $wiki_id     = absint( $_GET['wiki_id'] ?? 0 );
        $wiki        = $wiki_id > 0 ? get_post( $wiki_id ) : null;
        $title       = $wiki instanceof \WP_Post ? $wiki->post_title : '';
        $slug        = $wiki instanceof \WP_Post ? $wiki->post_name : '';
        $excerpt     = $wiki instanceof \WP_Post ? $wiki->post_excerpt : '';
        $wikis       = get_posts(
            array(
                'post_type'      => 'wikipress_wiki',
                'post_status'    => 'publish',
                'posts_per_page' => 25,
                'orderby'        => 'title',
                'order'          => 'ASC',
            )
        );

        $notice_messages = array(
            'wiki_saved'             => __( 'Wiki created successfully.', 'wikipress' ),
            'wiki_updated'           => __( 'Wiki updated successfully.', 'wikipress' ),
            'wiki_deleted'           => __( 'Wiki deleted successfully.', 'wikipress' ),
            'wiki_missing_title'     => __( 'Please provide a wiki title before saving.', 'wikipress' ),
            'wiki_missing_id'        => __( 'No wiki was selected to delete.', 'wikipress' ),
            'wiki_page_saved'        => __( 'Wiki page created successfully.', 'wikipress' ),
            'wiki_page_updated'      => __( 'Wiki page updated successfully.', 'wikipress' ),
            'wiki_page_deleted'      => __( 'Wiki page deleted successfully.', 'wikipress' ),
            'wiki_page_missing_wiki' => __( 'The selected wiki could not be found.', 'wikipress' ),
            'wiki_page_missing_title' => __( 'Please provide a wiki page title before saving.', 'wikipress' ),
            'wiki_page_missing_id'   => __( 'No wiki page was selected to delete.', 'wikipress' ),
            'wiki_error'             => __( 'The wiki could not be saved. Please try again.', 'wikipress' ),
            'wiki_page_error'        => __( 'The wiki page could not be saved. Please try again.', 'wikipress' ),
        );

        if ( isset( $notice_messages[ $wiki_notice ] ) ) {
            $notice_type = in_array( $wiki_notice, array( 'wiki_saved', 'wiki_updated', 'wiki_deleted', 'wiki_page_saved', 'wiki_page_updated', 'wiki_page_deleted' ), true ) ? 'success' : 'error';
            echo AlertHelper::get_admin_notice( $notice_messages[ $wiki_notice ], $notice_type );
        }
        ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h4 mb-1"><?php esc_html_e( 'Wikis', 'wikipress' ); ?></h2>
                        <p class="text-secondary mb-0"><?php esc_html_e( 'Create and manage the individual wiki entries on this WordPress site.', 'wikipress' ); ?></p>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="row g-3">
                    <?php wp_nonce_field( 'wikipress_save_wiki', 'wikipress_wiki_nonce' ); ?>
                    <input type="hidden" name="action" value="wikipress_save_wiki" />
                    <?php if ( $wiki instanceof \WP_Post ) : ?>
                        <input type="hidden" name="wikipress_wiki_id" value="<?php echo esc_attr( (string) $wiki->ID ); ?>" />
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label class="form-label" for="wikipress-wiki-title"><?php esc_html_e( 'Wiki title', 'wikipress' ); ?></label>
                        <input type="text" class="form-control" id="wikipress-wiki-title" name="wikipress_wiki[title]" value="<?php echo esc_attr( $title ); ?>" required />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="wikipress-wiki-slug"><?php esc_html_e( 'Wiki slug', 'wikipress' ); ?></label>
                        <input type="text" class="form-control" id="wikipress-wiki-slug" name="wikipress_wiki[slug]" value="<?php echo esc_attr( $slug ); ?>" />
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="wikipress-wiki-excerpt"><?php esc_html_e( 'Short description', 'wikipress' ); ?></label>
                        <textarea class="form-control" id="wikipress-wiki-excerpt" name="wikipress_wiki[excerpt]" rows="4"><?php echo esc_textarea( $excerpt ); ?></textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <?php if ( $wiki instanceof \WP_Post ) : ?>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) ); ?>" class="btn btn-outline-secondary"><?php esc_html_e( 'Cancel', 'wikipress' ); ?></a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary"><?php echo $wiki instanceof \WP_Post ? esc_html__( 'Update Wiki', 'wikipress' ) : esc_html__( 'Create Wiki', 'wikipress' ); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="h5 mb-3"><?php esc_html_e( 'Existing wikis', 'wikipress' ); ?></h3>
                <?php if ( empty( $wikis ) ) : ?>
                    <p class="text-secondary mb-0"><?php esc_html_e( 'No wikis have been created yet.', 'wikipress' ); ?></p>
                <?php else : ?>
                    <div class="list-group">
                        <?php foreach ( $wikis as $existing_wiki ) : ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold"><?php echo esc_html( get_the_title( $existing_wiki ) ); ?></div>
                                    <div class="small text-secondary"><?php echo esc_html( $existing_wiki->post_name ); ?></div>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&wiki_id=' . absint( $existing_wiki->ID ) ) ); ?>" class="btn btn-sm btn-outline-primary"><?php esc_html_e( 'Edit', 'wikipress' ); ?></a>
                                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="d-inline">
                                        <?php wp_nonce_field( 'wikipress_delete_wiki', 'wikipress_wiki_nonce' ); ?>
                                        <input type="hidden" name="action" value="wikipress_delete_wiki" />
                                        <input type="hidden" name="wikipress_wiki_id" value="<?php echo esc_attr( (string) $existing_wiki->ID ); ?>" />
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo esc_js( __( 'Delete this wiki? This action cannot be undone.', 'wikipress' ) ); ?>');"><?php esc_html_e( 'Delete', 'wikipress' ); ?></button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
	/**
     * Renders the modal for creating or editing a wiki.
     *
     * @since 1.0.0
     */
	public function render_wiki_modal(): void {
        // Render the modal for creating or editing a wiki here.
        ?>
        <div class="modal fade" id="wikiModal" tabindex="-1" aria-labelledby="wikiModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="wikiModalLabel"><?php esc_html_e( 'Wiki', 'wikipress' ); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php $this->render_page_content(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
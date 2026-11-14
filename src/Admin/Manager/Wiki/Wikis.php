<?php
/**
 * Wikis page renderer for the WikiPress admin.
 *
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Admin\Manager\Wiki\WikiManager;
use WikiPress\Includes\Functions\Helpers\AlertHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Plugins\FontAwesome\Includes\Functions\Helpers\FAHelper;
use WikiPress\Includes\Functions\Helpers\WPMediaHelper;

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
        $view        = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : 'cards';
        $view        = in_array( $view, array( 'cards', 'table' ), true ) ? $view : 'cards';
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
			$notice_type = in_array(
				$wiki_notice,
				array( 'wiki_saved', 'wiki_updated', 'wiki_deleted', 'wiki_page_saved', 'wiki_page_updated', 'wiki_page_deleted' ),
				true
			) ? 'success' : 'error';
			echo AlertHelper::get_admin_notice( $notice_messages[ $wiki_notice ], $notice_type );
		}
		?>
		<div class="card shadow-sm border-0 mb-4">
			<div class="card-body p-1">
				<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
					<div>
						<h2 class="h2 mb-1"><?php esc_html_e( 'Wiki Manager', 'wikipress' ); ?></h2>
						<p class="text-secondary mb-0"><?php esc_html_e( 'Create and manage the individual wikis.', 'wikipress' ); ?></p>
					</div>
					<?php echo FormFieldHelper::button(
						FAHelper::faicon( 'plus' ) . ' ' . __( 'Create New Wiki', 'wikipress' ),
						array(
							'type'      => 'button',
							'class'     => 'btn btn-primary btn-sm',
							'raw'       => true,
							'attributes' => array(
								'data-bs-toggle' => 'modal',
								'data-bs-target' => '#wikipress-wiki-modal',
							),
						)
					); ?>
				</div>
			</div>
		</div>
        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
			<h3 class="h5 mb-0"><?php esc_html_e( 'Wikis', 'wikipress' ); ?></h3>
			<div class="btn-group" role="group" aria-label="<?php esc_attr_e( 'Wiki view toggle', 'wikipress' ); ?>">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&view=cards' ) ); ?>" class="btn btn-sm <?php echo 'cards' === $view ? 'btn-primary' : 'btn-outline-secondary'; ?>"><?php esc_html_e( 'Cards', 'wikipress' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&view=table' ) ); ?>" class="btn btn-sm <?php echo 'table' === $view ? 'btn-primary' : 'btn-outline-secondary'; ?>"><?php esc_html_e( 'Table', 'wikipress' ); ?></a>
			</div>
		</div>
        <?php if ( empty( $wikis ) ) : ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5"><?php esc_html_e( 'No Wikis created yet', 'wikipress' ); ?></h2>
                        <p class="text-secondary mb-3"><?php esc_html_e( 'Create your first Wiki to start organising your knowledge.', 'wikipress' ); ?></p>
                        <?php echo FormFieldHelper::button(
                            FAHelper::faicon( 'plus' ) . ' ' . __( 'Create New Wiki', 'wikipress' ),
                            array(
                                'type'      => 'button',
                                'class'     => 'btn btn-primary btn-sm',
                                'raw'       => true,
                                'attributes' => array(
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#wikipress-wiki-modal',
                                ),
                            )
                        ); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php if ( 'table' === $view ) : ?>
                <?php $this->render_layout_table( $wikis ); ?>
            <?php elseif ( 'cards' === $view ) : ?>
                <?php $this->render_layout_cards( $wikis ); ?>
            <?php endif; ?>
				
        <?php endif; ?>


		<?php $this->render_wiki_modal( $wiki_id > 0 ? get_post( $wiki_id ) : null ); ?>

		<?php if ( $wiki_id > 0 ) : ?>
			<script>
				window.addEventListener('load', function () {
					var modalEl = document.getElementById('wikipress-wiki-modal');
					if ( modalEl && window.bootstrap && bootstrap.Modal) {
						new bootstrap.Modal(modalEl).show();
					}
				});
			</script>
		<?php endif; ?>
		<?php
	}

    private function render_layout_cards( array $wikis ): void {
        // This function can be used to render the layout for the wiki cards if needed.
            $this->render_wiki_cards( $wikis );
    }
    private function render_layout_table(array $wikis ): void {
        // This function can be used to render the layout for the wiki table if needed.
        ?>
        <div class="card shadow-sm border-0">
			<div class="card-body p-4">
				<?php $this->render_wiki_table( $wikis ); ?>
			</div>
		</div>
    <?php
    }

	/**
	 * Render the wiki list as cards.
	 *
	 * @param array $wikis Wiki posts.
	 * @return void
	 */
	private function render_wiki_cards( array $wikis ): void {
		?>
		<div class="row g-4">
			<?php foreach ( $wikis as $wiki ) : ?>
				<?php $author = get_userdata( $wiki->post_author ); ?>
				<div class="col-12 col-md-6 col-xl-4">
					<div class="card h-100 shadow-sm border-0">
						<div class="card-body d-flex flex-column gap-3">
							<div class="d-flex align-items-center gap-3">
								<div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
									<span class="fw-bold text-secondary"><?php echo esc_html( strtoupper( substr( get_the_title( $wiki ), 0, 1 ) ) ); ?></span>
								</div>
								<div class="flex-grow-1 min-w-0">
									<h4 class="h6 mb-1 text-truncate"><?php echo esc_html( get_the_title( $wiki ) ); ?></h4>
									<p class="small text-secondary mb-0"><?php echo esc_html( $wiki->post_name ); ?></p>
								</div>
							</div>
							<p class="text-secondary mb-0"><?php echo esc_html( $wiki->post_excerpt ?: __( 'No description provided.', 'wikipress' ) ); ?></p>
							<div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
								<span class="small text-secondary"><?php echo esc_html( $author ? $author->display_name : __( 'Unknown owner', 'wikipress' ) ); ?></span>
								<div class="btn-group" role="group" aria-label="<?php esc_attr_e( 'Wiki actions', 'wikipress' ); ?>">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&view=cards&wiki_id=' . absint( $wiki->ID ) ) ); ?>" class="btn btn-sm btn-outline-primary"><?php esc_html_e( 'Edit', 'wikipress' ); ?></a>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="d-inline">
										<?php wp_nonce_field( 'wikipress_delete_wiki', 'wikipress_wiki_nonce' ); ?>
										<input type="hidden" name="action" value="wikipress_delete_wiki" />
										<input type="hidden" name="wikipress_wiki_id" value="<?php echo esc_attr( (string) $wiki->ID ); ?>" />
										<button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo esc_js( __( 'Delete this wiki? This action cannot be undone.', 'wikipress' ) ); ?>');"><?php esc_html_e( 'Delete', 'wikipress' ); ?></button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render the wiki list as a table.
	 *
	 * @param array $wikis Wiki posts.
	 * @return void
	 */
	private function render_wiki_table( array $wikis ): void {
		?>
		<div class="table-responsive">
			<table class="table table-hover align-middle mb-0">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Logo', 'wikipress' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Name', 'wikipress' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Description', 'wikipress' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Owner', 'wikipress' ); ?></th>
						<th scope="col" class="text-end"><?php esc_html_e( 'Actions', 'wikipress' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $wikis as $wiki ) : ?>
						<?php $author = get_userdata( $wiki->post_author ); ?>
						<tr>
							<td style="width: 80px;">
								<div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:42px;height:42px;">
									<span class="fw-bold text-secondary"><?php echo esc_html( strtoupper( substr( get_the_title( $wiki ), 0, 1 ) ) ); ?></span>
								</div>
							</td>
							<td>
								<div class="fw-semibold"><?php echo esc_html( get_the_title( $wiki ) ); ?></div>
								<div class="small text-secondary"><?php echo esc_html( $wiki->post_name ); ?></div>
							</td>
							<td class="text-secondary"><?php echo esc_html( $wiki->post_excerpt ?: __( 'No description provided.', 'wikipress' ) ); ?></td>
							<td><?php echo esc_html( $author ? $author->display_name : __( 'Unknown owner', 'wikipress' ) ); ?></td>
							<td class="text-end">
								<div class="btn-group" role="group" aria-label="<?php esc_attr_e( 'Wiki actions', 'wikipress' ); ?>">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis&view=table&wiki_id=' . absint( $wiki->ID ) ) ); ?>" class="btn btn-sm btn-outline-primary"><?php esc_html_e( 'Edit', 'wikipress' ); ?></a>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="d-inline">
										<?php wp_nonce_field( 'wikipress_delete_wiki', 'wikipress_wiki_nonce' ); ?>
										<input type="hidden" name="action" value="wikipress_delete_wiki" />
										<input type="hidden" name="wikipress_wiki_id" value="<?php echo esc_attr( (string) $wiki->ID ); ?>" />
										<button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('<?php echo esc_js( __( 'Delete this wiki? This action cannot be undone.', 'wikipress' ) ); ?>');"><?php esc_html_e( 'Delete', 'wikipress' ); ?></button>
									</form>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the modal for creating or editing a wiki.
	 *
	 * @param \WP_Post|null $wiki The wiki post being edited.
	 * @return void
	 */
	public function render_wiki_modal( $wiki = null ): void {
		$wiki_id = $wiki instanceof \WP_Post ? absint( $wiki->ID ) : 0;
		$is_edit = $wiki_id > 0;
		$settings = $is_edit ? get_post_meta( $wiki_id, '_wikipress_wiki_settings', true ) : array();
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$category_selected = $is_edit ? wp_get_post_terms( $wiki_id, 'wikipress_category', array( 'fields' => 'ids' ) ) : array();
		$tag_selected      = $is_edit ? wp_get_post_terms( $wiki_id, 'wikipress_tag', array( 'fields' => 'ids' ) ) : array();
		$category_selected = array_map( 'absint', (array) $category_selected );
		$tag_selected      = array_map( 'absint', (array) $tag_selected );
		$category_options  = array();
		$tag_options       = array();
		$category_terms    = get_terms(
			array(
				'taxonomy'   => 'wikipress_category',
				'hide_empty' => false,
			)
		);
		$tag_terms         = get_terms(
			array(
				'taxonomy'   => 'wikipress_tag',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $category_terms ) ) {
			foreach ( $category_terms as $category_term ) {
				$category_options[ (string) $category_term->term_id ] = $category_term->name;
			}
		}

		if ( ! is_wp_error( $tag_terms ) ) {
			foreach ( $tag_terms as $tag_term ) {
				$tag_options[ (string) $tag_term->term_id ] = $tag_term->name;
			}
		}

		$wiki_data = array(
			'title'   => $is_edit ? $wiki->post_title : '',
			'slug'    => $is_edit ? $wiki->post_name : '',
			'excerpt' => $is_edit ? $wiki->post_excerpt : '',
		);

		$settings = array_merge(
			array(
				'logo'                => '',
				'navigation_position' => 'top',
				'internal_wiki'       => false,
				'permalink_override'  => false,
				'permalink'           => '',
			),
			$settings
		);

		$primary_rows = array(
			array(
				'name'        => 'wikipress_wiki[title]',
				'label'       => __( 'Wiki Name', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-title',
				'renderer'    => function () use ( $wiki_data ) {
					return FormFieldHelper::text_input(
						'wikipress_wiki[title]',
						$wiki_data['title'],
						array(
							'id'      => 'wikipress-wiki-title',
							'class'   => 'w-100',
							'required' => true,
						)
					);
				},
				'description' => __( 'Primary wiki title shown in the manager and front-end list.', 'wikipress' ),
				'hint'        => __( 'Use the public wiki name that best represents this knowledge base.', 'wikipress' ),
			),
			array(
				'name'        => 'wikipress_wiki[slug]',
				'label'       => __( 'Wiki Slug', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-slug',
				'renderer'    => function () use ( $wiki_data ) {
					return FormFieldHelper::text_input(
						'wikipress_wiki[slug]',
						$wiki_data['slug'],
						array(
							'id'    => 'wikipress-wiki-slug',
							'class' => 'w-100',
						)
					);
				},
				'description' => __( 'Unique slug used in the wiki URL and routing.', 'wikipress' ),
				'hint'        => __( 'Leave blank to auto-generate a slug from the wiki name.', 'wikipress' ),
			),
			array(
				'name'        => 'wikipress_wiki[excerpt]',
				'label'       => __( 'Wiki Description', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-excerpt',
				'renderer'    => function () use ( $wiki_data ) {
					return FormFieldHelper::textarea(
						'wikipress_wiki[excerpt]',
						$wiki_data['excerpt'],
						array(
							'id'    => 'wikipress-wiki-excerpt',
							'rows'  => 4,
							'class' => 'w-100',
						)
					);
				},
				'description' => __( 'Short summary that explains the purpose of the wiki.', 'wikipress' ),
				'hint'        => __( 'This text is used as the brief description for the wiki listing and overview panel.', 'wikipress' ),
			),
            array(
                'name'        => 'wikipress_wiki[category]',
                'label'       => __( 'Wiki Category', 'wikipress' ),
                'field_id'    => 'wikipress-wiki-category',
                'renderer'    => function () use ( $wiki_data ) {
                    return FormFieldHelper::bootstrap_multiselect(
                        'wikipress_wiki[category]',
                        array(
                            'data' => $category_options,
							'selected' => $category_selected,
							'id' => 'wikipress-wiki-category',
							'live_search' => true,
							'show_selected_tags' => true,
							'open_options' => true,
							'live_search_placeholder' => 'Search forcategories',
							'placeholder' => 'Search for categories',
                        )
                    );
                },
                'description' => __( 'Main content of the wiki.', 'wikipress' ),
                'hint'        => __( 'Provide the detailed content for this wiki.', 'wikipress' ),
            ),
            array(
                'name'        => 'wikipress_wiki[tags]',
                'label'       => __( 'Wiki Tags', 'wikipress' ),
                'field_id'    => 'wikipress-wiki-tags',
                'renderer'    => function () use ( $wiki_data ) {
                    return FormFieldHelper::bootstrap_multiselect(
                        'wikipress_wiki[tags]',
                        array(
                            'data' => $tag_options,
							'selected' => $tag_selected,
							'id' => 'wikipress-wiki-tags',
							'live_search' => true,
							'show_selected_tags' => true,
							'open_options' => true,
							'live_search_placeholder' => 'Search or create tags',
							'placeholder' => 'Search or create tags',
                        )
                    );
                },
                'description' => __( 'Tags associated with the wiki.', 'wikipress' ),
                'hint'        => __( 'Select or create relevant tags for this wiki.', 'wikipress' ),
            ),
		);

		$settings_rows = array(
			array(
				'name'        => 'wikipress_wiki[settings][logo]',
				'label'       => __( 'Wiki Logo', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-logo',
				'renderer'    => function () use ( $settings ) {
					return WPMediaHelper::create(
						$settings['logo'],
						array(
							'name'        => 'wikipress_wiki[settings][logo]',
							'id'          => 'wikipress-wiki-logo',
							'button_text' => __( 'Choose Logo', 'wikipress' ),
							'helper_text' => __( 'Upload a logo for this wiki.', 'wikipress' ),
							'show_remove' => true,
						),
						array(
							'alt' => __( 'Wiki logo preview', 'wikipress' ),
						)
					);
				},
				'description' => __( 'Upload a logo for this wiki.', 'wikipress' ),
				'hint'        => __( 'This logo can be shown in the wiki list and header.', 'wikipress' ),
			),
			array(
				'name'        => 'wikipress_wiki[settings][navigation_position]',
				'label'       => __( 'Navigation Position', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-navigation-position',
				'renderer'    => function () use ( $settings ) {
					return FormFieldHelper::select(
						'wikipress_wiki[settings][navigation_position]',
						array(
							'top'   => __( 'Top', 'wikipress' ),
							'left'  => __( 'Left', 'wikipress' ),
							'right' => __( 'Right', 'wikipress' ),
						),
						$settings['navigation_position'],
						array(
							'id' => 'wikipress-wiki-navigation-position',
						)
					);
				},
				'description' => __( 'Choose where the wiki navigation appears.', 'wikipress' ),
				'hint'        => __( 'This affects the position of the wiki navigation around the content.', 'wikipress' ),
			),
            array(
				'name'        => 'wikipress_wiki[settings][internal_wiki]',
				'label'       => __( 'Internal Wiki', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-internal-wiki',
				'renderer'    => function () use ( $settings ) {
					return FormFieldHelper::switch(
						'wikipress_wiki[settings][internal_wiki]',
						'1',
						__( 'Enabled', 'wikipress' ),
						array(
							'id'      => 'wikipress-wiki-internal-wiki',
							'checked' => ! empty( $settings['internal_wiki'] ),
						)
					);
				},
				'description' => __( 'Mark this wiki as internal.', 'wikipress' ),
				'hint'        => __( 'When enabled, this wiki is considered internal and may have restricted access.', 'wikipress' ),
			),
			array(
				'name'        => 'wikipress_wiki[settings][permalink_override]',
				'label'       => __( 'Custom Permalink Override', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-permalink-override',
				'renderer'    => function () use ( $settings ) {
					return FormFieldHelper::switch(
						'wikipress_wiki[settings][permalink_override]',
						'1',
						__( 'Enabled', 'wikipress' ),
						array(
							'id'      => 'wikipress-wiki-permalink-override',
							'checked' => ! empty( $settings['permalink_override'] ),
						)
					);
				},
				'description' => __( 'Override the global permalink template for this wiki.', 'wikipress' ),
				'hint'        => __( 'When enabled, this wiki can use its own permalink structure.', 'wikipress' ),
			),
			array(
				'name'        => 'wikipress_wiki[settings][permalink]',
				'label'       => __( 'Custom Permalink', 'wikipress' ),
				'field_id'    => 'wikipress-wiki-permalink',
				'renderer'    => function () use ( $settings ) {
					return FormFieldHelper::text_input(
						'wikipress_wiki[settings][permalink]',
						$settings['permalink'],
						array(
							'id'    => 'wikipress-wiki-permalink',
							'class' => 'w-100',
						)
					);
				},
				'description' => __( 'Optional custom permalink pattern for this wiki.', 'wikipress' ),
				'hint'        => __( 'Use a structure such as /docs/%wiki%/%page%/ or leave blank to use the global default.', 'wikipress' ),
			),
		);
		?>
		<div class="modal fade" id="wikipress-wiki-modal" tabindex="-1" aria-labelledby="wikipress-wiki-modal-title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<form id="wikipress-wiki-save-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="wikipress_save_wiki" />
						<input type="hidden" name="wikipress_wiki_id" value="<?php echo esc_attr( (string) $wiki_id ); ?>" />
						<?php wp_nonce_field( 'wikipress_save_wiki', 'wikipress_wiki_nonce' ); ?>
						<div class="modal-header">
							<h5 class="modal-title" id="wikipress-wiki-modal-title"><?php echo esc_html( $is_edit ? __( 'Edit Wiki', 'wikipress' ) : __( 'Create Wiki', 'wikipress' ) ); ?></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="accordion" id="wikipress-wiki-form-accordion">
								<div class="accordion-item">
									<h2 class="accordion-header" id="wikipress-wiki-info-heading">
										<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#wikipress-wiki-info" aria-expanded="true" aria-controls="wikipress-wiki-info">
											<?php esc_html_e( 'Wiki Info', 'wikipress' ); ?>
										</button>
									</h2>
									<div id="wikipress-wiki-info" class="accordion-collapse collapse show" aria-labelledby="wikipress-wiki-info-heading" data-bs-parent="#wikipress-wiki-form-accordion">
										<div class="accordion-body p-0">
											<?php $this->render_form_table_rows( $primary_rows ); ?>
										</div>
									</div>
								</div>
								<div class="accordion-item">
									<h2 class="accordion-header" id="wikipress-wiki-settings-heading">
										<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#wikipress-wiki-settings" aria-expanded="false" aria-controls="wikipress-wiki-settings">
											<?php esc_html_e( 'Wiki Settings', 'wikipress' ); ?>
										</button>
									</h2>
									<div id="wikipress-wiki-settings" class="accordion-collapse collapse" aria-labelledby="wikipress-wiki-settings-heading" data-bs-parent="#wikipress-wiki-form-accordion">
										<div class="accordion-body p-0">
											<?php $this->render_form_table_rows( $settings_rows ); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer d-flex justify-content-between align-items-center">
							<?php if ( $wiki_id > 0 ) : ?>
                                <?php echo FormFieldHelper::button( 
                                    FAHelper::faicon( 'trash' ) . ' ' . esc_html__( 'Delete', 'wikipress' ), 
                                    array( 
                                        'type' => 'button', 
                                        'class' => 'btn btn-outline-danger', 
                                        'raw' => true,
                                        'data-bs-dismiss' => 'modal',
                                        'onclick' => "if (confirm('" . esc_js( __( 'Delete this wiki? This action cannot be undone.', 'wikipress' ) ) . "')) { document.getElementById('wikipress-delete-wiki-form-" . esc_attr( (string) $wiki_id ) . "').submit(); }"
                                    ) 
                                ); ?>
								<button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal" onclick="if (confirm('<?php echo esc_js( __( 'Delete this wiki? This action cannot be undone.', 'wikipress' ) ); ?>')) { document.getElementById('wikipress-delete-wiki-form-<?php echo esc_attr( (string) $wiki_id ); ?>').submit(); }">
									<?php esc_html_e( 'Delete', 'wikipress' ); ?>
								</button>
							<?php endif; ?>
							<div class="d-flex gap-2 ms-auto">
                                <?php echo FormFieldHelper::button( 
                                    FAHelper::faicon( 'circle-xmark' ) . ' ' . esc_html__( 'Cancel', 'wikipress' ), 
                                    array( 
                                        'type' => 'button', 
                                        'class' => 'btn btn-outline-secondary', 
                                        'raw' => true,
                                        'data-bs-dismiss' => 'modal'
                                    ) 
                                ); ?>
								<?php echo FormFieldHelper::button( 
                                    FAHelper::faicon( 'floppy-disk' ) . ' ' . __( 'Save', 'wikipress' ), 
                                    array( 
                                        'type' => 'submit', 
                                        'class' => 'btn btn-primary', 
                                        'raw' => true 
                                    ) 
                                ); ?>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php if ( $wiki_id > 0 ) : ?>
			<form id="wikipress-delete-wiki-form-<?php echo esc_attr( (string) $wiki_id ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="d-none">
				<?php wp_nonce_field( 'wikipress_delete_wiki', 'wikipress_wiki_nonce' ); ?>
				<input type="hidden" name="action" value="wikipress_delete_wiki" />
				<input type="hidden" name="wikipress_wiki_id" value="<?php echo esc_attr( (string) $wiki_id ); ?>" />
			</form>
		<?php endif; ?>
		<?php
	}

    /**
	 * Render a complete set of table rows for a settings section.
	 *
	 * @param array $rows Row definitions with name, label, renderer, description, hint and optional field_id.
	 * @return void
	 */
	private function render_form_table_rows( array $rows ): void {
		if ( empty( $rows ) ) {
			return;
		}
		?>
		<table class="table table-borderless mb-0">
			<tbody>
				<?php foreach ( $rows as $row ) : ?>
					<?php
					$field_name = $row['name'] ?? '';
					$field_label = $row['label'] ?? '';
					$field_renderer = $row['renderer'] ?? null;
					$field_description = $row['description'] ?? '';
					$field_hint = $row['hint'] ?? '';
					$field_id = $row['field_id'] ?? 'wikipress-' . str_replace( array( '[', ']', '_' ), '-', sanitize_key( str_replace( 'wikipress_', '', $field_name ) ) );
					$field_html = is_callable( $field_renderer ) ? (string) call_user_func( $field_renderer ) : '';
					?>
                    <tr>
                        <th scope="row" class="align-top text-wrap pe-4" style="width: 35%;" >
                            <?php echo wp_kses_post(
                                FormFieldHelper::label(
                                    $field_id ?? '',
                                    $field_label ?? '',
                                    array(
                                        'description' => $field_description ?? '',
                                        'tooltip'     => $field_hint ?? '',
                                    )
                                )
                            ); ?>
                        </th>
                        <td style="width: 65%;" >
                            <?php echo $field_html; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}
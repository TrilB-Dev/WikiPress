<?php

namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Includes\Core\PostType;
use WikiPress\Includes\Core\Taxonomy;
use WikiPress\Includes\Functions\Helpers\QueryHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\TaxonomyHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiForms {
    public static function render_new_wiki_form( array $categories, array $tags, string $fields = '' ): void {
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-manage&wiki=new' ) ); ?>" class="wikipress-wiki-form card shadow-sm">
            <?php echo FormFieldHelper::nonce_field( 'wikipress_create_wiki', 'wikipress_create_wiki_nonce' ); ?>
            <?php echo FormFieldHelper::hidden( 'wikipress_action', 'create_wiki' ); ?>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12">
                        <h2 class="h5 mb-1">
                            <?php esc_html_e( 'Wiki Details', 'wikipress' ); ?>
                        </h2>
                        <p class="text-secondary mb-0">
                            <?php esc_html_e( 'Set the identity, content, and navigation style for this Wiki.', 'wikipress' ); ?>
                        </p>
                    </div>
                    <?php self::floating_text_field( 'name', __( 'Wiki Name', 'wikipress' ), '', true ); ?>
                    <?php self::floating_text_field( 'slug', __( 'Wiki Slug', 'wikipress' ) ); ?>
                    <div class="col-md-6">
                        <fieldset class="border rounded p-3">
                            <legend class="float-none w-auto px-2 fs-6 mb-0">
                                <?php esc_html_e( 'Wiki Categories', 'wikipress' ); ?>
                            </legend>
                            <?php echo FormFieldHelper::bootstrap_multiselect( 'wikipress_wiki[categories][]', [ 'data' => self::category_options( $categories ), 'class' => 'show-tick', 'icons_base' => 'fa-solid', 'tick_icon' => 'fa-check', 'live_search' => true, 'open_options' => true, 'placeholder' => __( 'Select or create categories', 'wikipress' ), 'live_search_placeholder' => __( 'Search or create categories', 'wikipress' ), 'attributes' => [ 'id' => 'wikipress-wiki-categories', 'data-wikipress-category-select' => 'true', 'data-wikipress-taxonomy-endpoint' => rest_url( 'wp/v2/' . Taxonomy::CATEGORY ), 'data-wikipress-taxonomy-create' => 'true', 'data-wikipress-rest-nonce' => wp_create_nonce( 'wp_rest' ) ] ] ); ?>
                            <?php echo FormFieldHelper::form_text( __( 'Search existing categories or create a new category from the picker.', 'wikipress' ) ); ?>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="border rounded p-3">
                            <legend class="float-none w-auto px-2 fs-6 mb-0">
                                <?php esc_html_e( 'Wiki Tags', 'wikipress' ); ?>
                            </legend>
                            <?php echo FormFieldHelper::bootstrap_multiselect( 'wikipress_wiki[tags][]', [ 'data' => array_map( static fn( $tag ) => [ 'value' => (string) $tag->term_id, 'label' => $tag->name ], $tags ), 'class' => 'show-tick', 'icons_base' => 'fa-solid', 'tick_icon' => 'fa-check', 'open_options' => true, 'placeholder' => __( 'Search or select tags', 'wikipress' ), 'live_search_placeholder' => __( 'Search or create tags', 'wikipress' ), 'attributes' => [ 'id' => 'wikipress-wiki-tags', 'data-wikipress-taxonomy-endpoint' => rest_url( 'wp/v2/' . Taxonomy::TAG ), 'data-wikipress-taxonomy-create' => 'true', 'data-wikipress-rest-nonce' => wp_create_nonce( 'wp_rest' ) ] ] ); ?>
                            <?php echo FormFieldHelper::form_text( __( 'Search existing tags or create a new tag from the picker.', 'wikipress' ) ); ?>
                        </fieldset>
                    </div>
                <div class="col-12">
                    <?php FormFieldHelper::tinymce( 'wikipress-wiki-excerpt', 'wikipress_wiki[excerpt]', __( 'Wiki Excerpt', 'wikipress' ), '', 6 ); ?>
                </div>
                <div class="col-12">
                    <?php //FormFieldHelper::tinymce( 'wikipress-wiki-description', 'wikipress_wiki[description]', __( 'Wiki Description', 'wikipress' ), '', 10 ); ?>
                </div>
                <div class="col-md-6">
                    <?php self::render_media_field( 'thumbnail_id', __( 'Wiki Header Image', 'wikipress' ), __( 'Select the featured image displayed for this Wiki.', 'wikipress' ) ); ?>
                </div>
                <div class="col-md-6">
                    <?php self::render_media_field( 'logo_id', __( 'Wiki Logo', 'wikipress' ), __( 'Select an optional logo for this Wiki.', 'wikipress' ) ); ?>
                </div>
                <div class="col-12">
                    <fieldset>
                        <legend class="form-label">
                            <?php esc_html_e( 'Wiki Navigation Style', 'wikipress' ); ?>
                        </legend>
                        <div class="d-flex flex-wrap gap-3">
                            <?php echo FormFieldHelper::radio(
                                'wikipress_wiki[navigation]',
                                'horizontal',
                                __( 'Horizontal - Along the top', 'wikipress' ),
                                [
                                    'checked' => true,
                                    'inline'  => true,
                                ]
                            ); ?>
                            <?php echo FormFieldHelper::radio(
                                'wikipress_wiki[navigation]',
                                'vertical',
                                __( 'Vertical - Sidebar', 'wikipress' ),
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                    </fieldset>
                </div>
                <?php if ( $fields ) : ?><div class="col-12"><h2 class="h5"><?php esc_html_e( 'Wiki Plugin Fields', 'wikipress' ); ?></h2><?php echo $fields; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <?php echo FormFieldHelper::button(
                    __( 'Cancel', 'wikipress' ),
                    [
                        'href'  => admin_url( 'admin.php?page=wikipress-manage' ),
                        'class' => 'btn btn-outline-secondary',
                    ]
                ); ?>
                <?php echo FormFieldHelper::button(
                    __( 'Create Wiki', 'wikipress' ),
                    [
                        'type'  => 'submit',
                        'class' => 'btn btn-primary',
                    ]
                ); ?>
            </div>
        </form>
        <?php
    }

    private static function floating_text_field( string $key, string $label, string $value = '', bool $required = false, string $name = 'wikipress_wiki' ): void {
        $id = 'wikipress-wiki-' . sanitize_key( $key );
        $field_name = $name . '[' . $key . ']';
        $control = FormFieldHelper::text_input( $field_name, $value, [
            'id' => $id,
            'placeholder' => $label,
            'required' => $required,
        ] );
        echo '<div class="col-md-6">' . FormFieldHelper::floating( $control, $label, [ 'for' => $id ] ) . '</div>';
    }

    private static function category_options( array $categories, int $parent = 0, int $level = 0 ): array {
        $options = [];

        foreach ( $categories as $category ) {
            if ( (int) $category->parent !== $parent ) {
                continue;
            }

            $options[] = [
                'value' => (string) $category->term_id,
                'label' => ( $level > 0 ? str_repeat( '-- ', $level ) : '' ) . $category->name,
            ];
            $options = array_merge( $options, self::category_options( $categories, (int) $category->term_id, $level + 1 ) );
        }

        return $options;
    }

    private static function render_media_field( string $key, string $label, string $description ): void {
        $id = 'wikipress-wiki-' . $key;
        ?>
        <div class="wikipress-media-field">
            <?php echo FormFieldHelper::label( $id, $label ); ?>
            <?php echo FormFieldHelper::input( 'wikipress_wiki[' . $key . ']', '', [ 'type' => 'hidden', 'id' => $id, 'value' => '' ] ); ?>
            <div class="d-flex align-items-center gap-2">
                <?php echo FormFieldHelper::button( __( 'Choose Image', 'wikipress' ), [
                    'type' => 'button',
                    'class' => 'btn btn-outline-secondary',
                    'data-wikipress-media-picker' => true,
                    'data-media-target' => $id,
                ] ); ?>
                <?php echo FormFieldHelper::button( __( 'Clear', 'wikipress' ), [
                    'type' => 'button',
                    'class' => 'btn btn-link text-danger d-none',
                    'data-wikipress-media-clear' => true,
                    'data-media-target' => $id,
                ] ); ?>
            </div>
            <?php echo FormFieldHelper::form_text( $description ); ?>
            <div class="mt-2" data-media-preview="<?php echo esc_attr( $id ); ?>"></div>
        </div>
        <?php
    }

    public static function getWikiForm( $wikiId = null ): array {
        $form = [
            'title' => __( 'Wiki Form', 'wikipress' ),
            'fields' => [
                'title' => [
                    'label' => __( 'Title', 'wikipress' ),
                    'type' => 'text',
                    'required' => true,
                ],
                'content' => [
                    'label' => __( 'Content', 'wikipress' ),
                    'type' => 'textarea',
                    'required' => true,
                ],
                // Add more fields as needed
            ],
        ];

        $form['submit_label'] = $wikiId ? __( 'Update Wiki', 'wikipress' ) : __( 'Create Wiki', 'wikipress' );

        return $form;
    }

    public static function render_modals( \WP_Post $wiki ): void {
        $settings_id = 'wikipress-wiki-settings-' . $wiki->ID;
        $manage_id = 'wikipress-wiki-manage-' . $wiki->ID;
        ?>
        <div class="modal fade" id="<?php echo esc_attr( $settings_id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $settings_id ); ?>-title" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header"><h2 class="modal-title h5" id="<?php echo esc_attr( $settings_id ); ?>-title"><?php /* translators: %s is the Wiki title. */ printf( esc_html__( '%s Settings', 'wikipress' ), esc_html( get_the_title( $wiki ) ) ); ?></h2><?php echo FormFieldHelper::button( '', [ 'type' => 'button', 'class' => 'btn-close', 'data-bs-dismiss' => 'modal', 'aria-label' => __( 'Close', 'wikipress' ), 'raw' => true ] ); ?></div>
                    <div class="modal-body">
                        <div class="accordion" id="<?php echo esc_attr( $settings_id ); ?>-accordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header"><?php echo FormFieldHelper::button( __( 'Wiki Details', 'wikipress' ), [ 'type' => 'button', 'class' => 'accordion-button', 'data-bs-toggle' => 'collapse', 'data-bs-target' => '#' . $settings_id . '-details', 'aria-expanded' => true ] ); ?></h3>
                                <div id="<?php echo esc_attr( $settings_id ); ?>-details" class="accordion-collapse collapse show"><div class="accordion-body"><div class="row g-3">
                                    <?php self::floating_text_field( 'name', __( 'Wiki Name', 'wikipress' ), $wiki->post_title, false, 'wikipress_wiki_settings' ); ?>
                                    <?php self::floating_text_field( 'slug', __( 'Wiki Slug', 'wikipress' ), $wiki->post_name, false, 'wikipress_wiki_settings' ); ?>
                                    <?php self::floating_text_field( 'navigation', __( 'Navigation Style', 'wikipress' ), get_post_meta( $wiki->ID, '_wikipress_navigation_style', true ) ?: 'horizontal', false, 'wikipress_wiki_settings' ); ?>
                                    <div class="col-12"><?php echo FormFieldHelper::form_text( __( 'The full Wiki editor is available from the Manage Wiki tabs. Save this panel to persist Wiki-level settings.', 'wikipress' ) ); ?></div>
                                </div></div></div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header"><?php echo FormFieldHelper::button( __( 'Global WikiPress Settings', 'wikipress' ), [ 'type' => 'button', 'class' => 'accordion-button collapsed', 'data-bs-toggle' => 'collapse', 'data-bs-target' => '#' . $settings_id . '-global' ] ); ?></h3>
                                <div id="<?php echo esc_attr( $settings_id ); ?>-global" class="accordion-collapse collapse"><div class="accordion-body">
                                    <?php foreach ( [ 'permalink' => __( 'Permalink', 'wikipress' ), 'search' => __( 'Wiki Search', 'wikipress' ), 'page' => __( 'Wiki Page', 'wikipress' ) ] as $key => $label ) : ?>
                                        <div class="row align-items-center g-3 border-bottom py-3">
                                            <div class="col-md-5">
                                                <?php echo FormFieldHelper::label(
                                                    $settings_id . '-' . $key . '-value',
                                                    $label,
                                                    [
                                                        'class' => 'form-label mb-1',
                                                        'description' => __( 'Use the global WikiPress value unless this Wiki needs an override.', 'wikipress' ),
                                                        'tooltip' => __( 'Apply the global WikiPress setting for this item, or override it specifically for this Wiki.', 'wikipress' ),
                                                    ]
                                                ); ?>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                                    <?php echo FormFieldHelper::switch(
                                                        'wikipress_wiki_settings[' . $key . '][use_global]',
                                                        '1',
                                                        __( 'Use Global', 'wikipress' ),
                                                        [
                                                            'id' => $settings_id . '-' . $key . '-global',
                                                            'checked' => true,
                                                            'data-wikipress-use-global' => true,
                                                            'class' => 'form-check-input',
                                                        ]
                                                    ); ?>
                                                    <?php echo FormFieldHelper::text_input(
                                                        'wikipress_wiki_settings[' . $key . '][value]',
                                                        '',
                                                        [
                                                            'class' => 'form-control',
                                                            'disabled' => true,
                                                            'data-wikipress-global-value' => true,
                                                        ]
                                                    ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?php echo FormFieldHelper::button( __( 'Cancel', 'wikipress' ), [
                            'type' => 'button',
                            'class' => 'btn btn-outline-secondary',
                            'data-bs-dismiss' => 'modal',
                        ] ); ?>
                        <?php echo FormFieldHelper::button( __( 'Save', 'wikipress' ), [
                            'type' => 'button',
                            'class' => 'btn btn-primary',
                            'data-wikipress-save-wiki-settings' => (string) $wiki->ID,
                        ] ); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="<?php echo esc_attr( $manage_id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $manage_id ); ?>-title" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title h5" id="<?php echo esc_attr( $manage_id ); ?>-title"><?php /* translators: %s is the wiki title. */ printf( esc_html__( 'Manage %s', 'wikipress' ), esc_html( get_the_title( $wiki ) ) ); ?></h2><?php echo FormFieldHelper::button( '', [ 'type' => 'button', 'class' => 'btn-close', 'data-bs-dismiss' => 'modal', 'aria-label' => __( 'Close', 'wikipress' ), 'raw' => true ] ); ?></div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist"><?php foreach ( [ 'pages' => __( 'Pages', 'wikipress' ), 'categories' => __( 'Categories', 'wikipress' ), 'tags' => __( 'Tags', 'wikipress' ), 'navigation' => __( 'Navigation', 'wikipress' ) ] as $tab => $label ) : ?><li class="nav-item" role="presentation"><?php echo FormFieldHelper::button( $label, [ 'type' => 'button', 'class' => 'nav-link ' . ( 'pages' === $tab ? 'active' : '' ), 'data-bs-toggle' => 'tab', 'data-bs-target' => '#' . $manage_id . '-' . $tab, 'role' => 'tab' ] ); ?></li><?php endforeach; ?></ul>
                    <div class="tab-content pt-3">
                        <div class="tab-pane fade show active" id="<?php echo esc_attr( $manage_id ); ?>-pages" role="tabpanel"><?php self::render_pages( $wiki ); ?></div>
                        <div class="tab-pane fade" id="<?php echo esc_attr( $manage_id ); ?>-categories" role="tabpanel"><?php self::render_terms( $wiki, Taxonomy::CATEGORY, __( 'Category', 'wikipress' ) ); ?></div>
                        <div class="tab-pane fade" id="<?php echo esc_attr( $manage_id ); ?>-tags" role="tabpanel"><?php self::render_terms( $wiki, Taxonomy::TAG, __( 'Tag', 'wikipress' ) ); ?></div>
                        <div class="tab-pane fade" id="<?php echo esc_attr( $manage_id ); ?>-navigation" role="tabpanel"><div class="alert alert-secondary"><?php esc_html_e( 'Add Wiki Pages, Categories, Tags, and custom links to build the navigation menu.', 'wikipress' ); ?></div><div class="border rounded p-3" data-wikipress-menu-builder><div class="fw-semibold mb-2"><?php esc_html_e( 'Wiki Navigation', 'wikipress' ); ?></div><ol class="list-group list-group-numbered mb-3" data-wikipress-nav-items></ol><div class="d-flex flex-wrap gap-2"><?php echo FormFieldHelper::button( __( 'Add Wiki Pages', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-sm btn-outline-secondary', 'data-wikipress-nav-add' => 'pages', 'data-wikipress-nav-add-label' => __( 'Wiki Pages', 'wikipress' ) ] ); ?><?php echo FormFieldHelper::button( __( 'Add Wiki Categories', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-sm btn-outline-secondary', 'data-wikipress-nav-add' => 'categories', 'data-wikipress-nav-add-label' => __( 'Wiki Categories', 'wikipress' ) ] ); ?><?php echo FormFieldHelper::button( __( 'Add Wiki Tags', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-sm btn-outline-secondary', 'data-wikipress-nav-add' => 'tags', 'data-wikipress-nav-add-label' => __( 'Wiki Tags', 'wikipress' ) ] ); ?><?php echo FormFieldHelper::button( __( 'Add Custom Link', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-sm btn-outline-secondary', 'data-wikipress-nav-add' => 'custom', 'data-wikipress-nav-add-label' => __( 'Wiki Custom', 'wikipress' ) ] ); ?></div></div></div>
                    </div>
                </div>
            </div></div>
        </div>
        <?php
    }

    private static function render_pages( \WP_Post $wiki ): void {
        $pages = QueryHelper::posts( [ 'post_type' => PostType::PAGE, 'post_status' => 'any', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'meta_key' => '_wikipress_wiki_id', 'meta_value' => $wiki->ID ] )->posts;
        echo '<div class="d-flex justify-content-between align-items-center mb-3"><h3 class="h6 mb-0">' . esc_html__( 'Wiki Pages', 'wikipress' ) . '</h3>' . FormFieldHelper::button( __( 'Add New Page', 'wikipress' ), [ 'href' => admin_url( 'admin.php?page=wikipress-manage&wiki=page-new&wiki_id=' . $wiki->ID ), 'class' => 'btn btn-primary btn-sm' ] ) . '</div><div class="table-responsive"><table class="table align-middle"><thead><tr><th>' . esc_html__( 'Title', 'wikipress' ) . '</th><th>' . esc_html__( 'Status', 'wikipress' ) . '</th><th class="text-end">' . esc_html__( 'Actions', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( $pages as $page ) {
            echo '<tr><td>' . esc_html( get_the_title( $page ) ) . '</td><td>' . esc_html( ucfirst( $page->post_status ) ) . '</td><td class="text-end">' . FormFieldHelper::button( __( 'Edit', 'wikipress' ), [ 'href' => admin_url( 'admin.php?page=wikipress-manage&wiki=page-edit&wiki_id=' . $wiki->ID . '&page_id=' . $page->ID ), 'class' => 'btn btn-sm btn-outline-secondary' ] ) . ' ' . FormFieldHelper::button( __( 'Delete', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-sm btn-outline-danger', 'data-wikipress-delete-page' => (string) $page->ID ] ) . '</td></tr>';
        }
        if ( ! $pages ) echo '<tr><td colspan="3" class="text-secondary">' . esc_html__( 'No Wiki Pages have been created yet.', 'wikipress' ) . '</td></tr>';
        echo '</tbody></table></div>';
    }

    private static function render_terms( \WP_Post $wiki, string $taxonomy, string $label ): void {
        $terms = TaxonomyHelper::terms( $taxonomy, $wiki->ID );
        echo '<div class="d-flex justify-content-between align-items-center mb-3"><h3 class="h6 mb-0">' . esc_html( $label . 's' ) . '</h3>' . FormFieldHelper::button( __( 'Add New', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-primary btn-sm', 'data-wikipress-add-term' => $taxonomy ] ) . '</div><div class="table-responsive"><table class="table align-middle"><thead><tr><th>' . esc_html__( 'Name', 'wikipress' ) . '</th><th>' . esc_html__( 'Slug', 'wikipress' ) . '</th><th class="text-end">' . esc_html__( 'Actions', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( $terms as $term ) echo '<tr><td>' . esc_html( $term->name ) . '</td><td>' . esc_html( $term->slug ) . '</td><td class="text-end">' . FormFieldHelper::button( __( 'Edit', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-sm btn-outline-secondary', 'data-wikipress-edit-term' => (string) $term->term_id, 'data-wikipress-term-name' => $term->name, 'data-wikipress-term-slug' => $term->slug, 'data-wikipress-taxonomy' => $taxonomy ] ) . ' ' . FormFieldHelper::button( __( 'Delete', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-sm btn-outline-danger', 'data-wikipress-delete-term' => (string) $term->term_id, 'data-wikipress-taxonomy' => $taxonomy ] ) . '</td></tr>';
        if ( ! $terms ) echo '<tr><td colspan="3" class="text-secondary">' . esc_html__( 'No terms are assigned to this Wiki yet.', 'wikipress' ) . '</td></tr>';
        echo '</tbody></table></div><div class="collapse mt-3" data-wikipress-term-form data-wikipress-taxonomy="' . esc_attr( $taxonomy ) . '"><div class="border rounded p-3"><div class="row g-3"><div class="col-md-6">' . FormFieldHelper::label( '', $label . ' ' . __( 'Name', 'wikipress' ) ) . FormFieldHelper::text_input( '', '', [ 'class' => 'form-control', 'data-wikipress-term-name' => true ] ) . '</div><div class="col-md-6">' . FormFieldHelper::label( '', __( 'Slug', 'wikipress' ) ) . FormFieldHelper::text_input( '', '', [ 'class' => 'form-control', 'data-wikipress-term-slug' => true ] ) . '</div><div class="col-12">' . FormFieldHelper::label( '', __( 'Description', 'wikipress' ) ) . FormFieldHelper::textarea( '', '', [ 'class' => 'form-control', 'rows' => 3, 'data-wikipress-term-description' => true ] ) . '</div></div><div class="d-flex justify-content-end gap-2 mt-3">' . FormFieldHelper::button( __( 'Cancel', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-outline-secondary', 'data-wikipress-cancel-term' => true ] ) . FormFieldHelper::button( __( 'Save', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn btn-primary', 'data-wikipress-save-term' => true ] ) . '</div></div></div>';
    }
}
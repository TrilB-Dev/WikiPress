<?php

namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Assets\Assets;
use WikiPress\Includes\Core\PostType;
use WikiPress\Includes\Core\Taxonomy;
use WikiPress\Includes\Core\Editor;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\PostHelper;
use WikiPress\Includes\Functions\Helpers\QueryHelper;
use WikiPress\Includes\Functions\Helpers\TaxonomyHelper;
use WikiPress\Includes\Functions\Admin\FunctionsWiki;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class WikiManager extends Manager {
	/**
	 * Functions related to Wiki operations.
	 *
	 * @var FunctionsWiki
	 */
	private FunctionsWiki $wiki_functions;
	/**
	 * Constructor.
	 *
	 * @param FunctionsWiki $wiki_functions Functions related to Wiki operations.
	 */
	public function __construct( FunctionsWiki $wiki_functions ) {
		$this->wiki_functions = $wiki_functions;
	}
	/**
	 * Registers the assets for the Wiki Manager.
	 *
	 * @param Assets $assets The assets manager instance.
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, [ 'wikipress-manage' ], 'wiki' );
		$this->register_page_assets( $assets, [ 'wikipress-manage' ], 'navbuilder' );
		add_action( 'admin_enqueue_scripts', static function (): void {
			if ( 'wikipress-manage' === sanitize_key( $_GET['page'] ?? '' ) ) {
				wp_enqueue_media();
			}
		} );
	}
	/**
	 * Renders the Wiki Manager interface.
	 */
	public function render(): void {
		$wiki_action = sanitize_key( wp_unslash( $_GET['wiki'] ?? '' ) );
		if ( 'new' === $wiki_action ) {
			$this->render_new_wiki();
			return;
		}
		if ( in_array( $wiki_action, [ 'page-new', 'page-edit' ], true ) ) {
			$this->render_page_editor();
			return;
		}

		$this->header( __( 'Manage Wiki', 'wikipress' ) );
		$wikis = get_posts( [
			'post_type'      => PostType::WIKI,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );
		?>
		<div class="row g-4">
			<?php if ( $wikis ) : ?>
				<?php foreach ( $wikis as $wiki ) : ?>
					<?php $this->render_wiki_card( $wiki ); ?>
					<?php WikiForms::render_modals( $wiki ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="col-12">
					<div class="card border-0 shadow-sm">
						<div class="card-body p-4">
							<h2 class="h5"><?php esc_html_e( 'No Wikis created yet', 'wikipress' ); ?></h2>
							<p class="text-secondary mb-3"><?php esc_html_e( 'Create your first Wiki to start organising your knowledge.', 'wikipress' ); ?></p>
							<a class="btn btn-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-manage&wiki=new' ) ); ?>"><?php esc_html_e( 'Get Started', 'wikipress' ); ?></a>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
		$this->footer();
	}

	/**
	 * Renders the form to create a new Wiki.
	 */
	private function render_new_wiki(): void {
		$notice = $this->wiki_functions->save_wiki();
		$this->header( __( 'Create a New Wiki', 'wikipress' ) );
		if ( $notice ) {
			echo wp_kses_post( $notice );
		}
		$categories = TaxonomyHelper::terms( Taxonomy::CATEGORY );
		$tags = TaxonomyHelper::terms( Taxonomy::TAG );
		$fields = apply_filters( 'wikipress_wiki_form_fields', '', null );
		WikiForms::render_new_wiki_form( $categories, $tags, $fields );
		$this->footer();
	}

	/**
	 * Renders the editor for a Wiki page.
	 */
	private function render_page_editor(): void {
		$wiki_id = absint( wp_unslash( $_GET['wiki_id'] ?? 0 ) );
		$page_id = absint( wp_unslash( $_GET['page_id'] ?? 0 ) );
		$page = $page_id ? get_post( $page_id ) : null;
		if ( $page && ( ! PostHelper::is_wiki_page( $page ) || (int) get_post_meta( $page_id, '_wikipress_wiki_id', true ) !== $wiki_id ) ) {
			$page = null;
		}
		if ( Editor::save_wiki_page( $wiki_id, $page_id ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wikipress-manage' ) );
			exit;
		}
		$this->header( $page ? __( 'Edit Wiki Page', 'wikipress' ) : __( 'Create Wiki Page', 'wikipress' ) );
		Editor::render_wiki_page_form( $page );
		$this->footer();
	}

	/**
	 * Renders a card for a Wiki.
	 *
	 * @param \WP_Post $wiki The Wiki post object.
	 */
	private function render_wiki_card( \WP_Post $wiki ): void {
		$page_count = QueryHelper::posts( [
			'post_type'      => PostType::PAGE,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => false,
			'meta_key'       => '_wikipress_wiki_id',
			'meta_value'     => $wiki->ID,
		] )->found_posts;
		$image_url = get_the_post_thumbnail_url( $wiki, 'medium' );
		$logo_id = absint( get_post_meta( $wiki->ID, '_wikipress_logo_id', true ) );
		$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
		$author_url = get_avatar_url( $wiki->post_author, [ 'size' => 64 ] );
		$description = wp_trim_words( wp_strip_all_tags( $wiki->post_excerpt ?: $wiki->post_content ), 24 );
		$settings_id = 'wikipress-wiki-settings-' . $wiki->ID;
		$manage_id = 'wikipress-wiki-manage-' . $wiki->ID;
		?>
		<div class="col-12 col-md-6 col-xl-4 d-flex">
			<article class="card wikipress-wiki-card text-start shadow-sm h-100 w-100">
				<div class="card-header d-flex align-items-center justify-content-between gap-2">
					<span class="fw-semibold"><?php echo esc_html( get_the_title( $wiki ) ); ?></span>
					<span class="badge text-bg-<?php echo 'publish' === $wiki->post_status ? 'success' : 'secondary'; ?>"><?php echo esc_html( ucfirst( $wiki->post_status ) ); ?></span>
				</div>
				<div class="card-body d-flex flex-column">
					<?php if ( $logo_url ) : ?>
						<img src="<?php echo esc_url( $logo_url ); ?>" class="wikipress-wiki-image rounded mx-auto d-block mb-3" alt="<?php echo esc_attr( get_the_title( $wiki ) ); ?>">
					<?php elseif ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" class="wikipress-wiki-image rounded mx-auto d-block mb-3" alt="<?php echo esc_attr( get_the_title( $wiki ) ); ?>">
					<?php else : ?>
						<div class="wikipress-wiki-image wikipress-wiki-image-placeholder rounded mx-auto d-flex align-items-center justify-content-center mb-3" aria-hidden="true"><span class="dashicons dashicons-book-alt"></span></div>
					<?php endif; ?>
					<p class="card-text text-secondary">
						<?php echo esc_html( $description ?: __( 'No description provided yet.', 'wikipress' ) ); ?>
					</p>
					<div class="d-flex align-items-center gap-2 mb-3">
						<img src="<?php echo esc_url( $author_url ); ?>" class="wikipress-author-image rounded-circle" alt="">
						<p class="card-text mb-0"><span class="text-secondary"><?php esc_html_e( 'Author:', 'wikipress' ); ?></span> <?php echo esc_html( get_the_author_meta( 'display_name', $wiki->post_author ) ); ?></p>
					</div>
					<p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Created:', 'wikipress' ); ?></span> <?php echo esc_html( get_the_date( '', $wiki ) ); ?></p>
					<div class="d-flex justify-content-between gap-3 mt-auto">
						<p class="card-text mb-0"><span class="text-secondary"><?php esc_html_e( 'Pages:', 'wikipress' ); ?></span> <?php echo esc_html( number_format_i18n( $page_count ) ); ?></p>
						<p class="card-text mb-0"><span class="text-secondary"><?php esc_html_e( 'Visitors:', 'wikipress' ); ?></span> 0</p>
					</div>
				</div>
				<div class="card-footer d-flex flex-wrap gap-2">
					<?php echo FormFieldHelper::button( __( 'Settings', 'wikipress' ), [
						'type'            => 'button',
						'class'           => 'btn btn-outline-secondary btn-sm',
						'data-bs-toggle'  => 'modal',
						'data-bs-target'  => '#' . $settings_id,
					] ); ?>
					<?php echo FormFieldHelper::button( sprintf( __( 'Manage %s', 'wikipress' ), get_the_title( $wiki ) ), [
						'type'            => 'button',
						'class'           => 'btn btn-primary btn-sm',
						'data-bs-toggle'  => 'modal',
						'data-bs-target'  => '#' . $manage_id,
					] ); ?>
					<?php echo FormFieldHelper::button( sprintf( __( 'Delete %s', 'wikipress' ), get_the_title( $wiki ) ), [
						'type'                     => 'button',
						'class'                    => 'btn btn-outline-danger btn-sm ms-auto',
						'data-wikipress-delete-wiki' => (string) $wiki->ID,
					] ); ?>
				</div>
			</article>
		</div>
		<?php
	}
}

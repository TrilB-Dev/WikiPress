<?php
/**
 * Dashboard page renderer for the WikiPress admin.
 *
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Admin\Manager\Wiki\WikiManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Dashboard extends WikiManager {
    /**
	 * Render the mod dashboard overview.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
	?>
		<div class="row g-4">
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm border-0 h-100">
					<div class="card-body">
						<p class="text-muted text-uppercase small fw-semibold mb-2"><?php esc_html_e( 'Total Wiki Pages', 'modpress' ); ?></p>
						<h2 class="display-6 mb-0"><?php echo esc_html('0'); ?></h2>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm border-0 h-100">
					<div class="card-body">
						<p class="text-muted text-uppercase small fw-semibold mb-2"><?php esc_html_e( 'Published', 'modpress' ); ?></p>
						<h2 class="display-6 mb-0"><?php echo esc_html('0'); ?></h2>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm border-0 h-100">
					<div class="card-body">
						<p class="text-muted text-uppercase small fw-semibold mb-2"><?php esc_html_e( 'Drafts', 'modpress' ); ?></p>
						<h2 class="display-6 mb-0"><?php echo esc_html('0'); ?></h2>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-xl-3">
				<div class="card shadow-sm border-0 h-100">
					<div class="card-body">
						<p class="text-muted text-uppercase small fw-semibold mb-2"><?php esc_html_e( 'Actions', 'modpress' ); ?></p>
						<a class="btn btn-primary btn-sm" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress&group=wiki-manager&tab=wikis' ) ); ?>"><?php esc_html_e( 'Manage wikis', 'modpress' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
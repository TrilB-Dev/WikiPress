<?php
/**
 * Dashboard main UI for the WikiPress plugin.
 *
 * @package WikiPress\Admin\Manager\Dashboard
 */
namespace WikiPress\Admin\Manager\Dashboard;


use WikiPress\Admin\Manager\Dashboard\DashboardManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardMain extends DashboardManager {
	/**
	 * Render the dashboard UI content.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$this->header( __( 'WikiPress Dashboard', 'wikipress' ) );
		$this->render_summary();
		$this->render_cards();
		$this->footer();
	}

	/**
	 * Render the key WikiPress overview metrics for users, groups, and roles.
	 *
	 * @return void
	 */
	protected function render_summary(): void {
	
	}

	/**
	 * Render quick actions for the WikiPress portal configuration.
	 *
	 * @return void
	 */
	protected function render_cards(): void {
		$cards = array(
			array(
				'title'       => __( 'User groups', 'wikipress' ),
				'description' => __( 'Create and manage membership groups for access control and portal segmentation.', 'wikipress' ),
				'icon'        => 'dashicons-groups',
				'url'         => admin_url( 'admin.php?page=wikipress&group=user-management&tab=user-groups' ),
				'priority'    => 10,
			),
			array(
				'title'       => __( 'User roles', 'wikipress' ),
				'description' => __( 'Adjust role combinations and ensure users stay within the configured maximum role count.', 'wikipress' ),
				'icon'        => 'dashicons-shield',
				'url'         => admin_url( 'admin.php?page=wikipress&group=user-management&tab=user-roles' ),
				'priority'    => 20,
			),
			array(
				'title'       => __( 'Login', 'wikipress' ),
				'description' => __( 'Review and manage the frontend login experience and authentication flow.', 'wikipress' ),
				'icon'        => 'dashicons-admin-network',
				'url'         => admin_url( 'admin.php?page=wikipress&group=user-management&tab=user-login' ),
				'priority'    => 30,
			),
			array(
				'title'       => __( 'Registration', 'wikipress' ),
				'description' => __( 'Configure the custom WikiPress registration and activation workflow.', 'wikipress' ),
				'icon'        => 'dashicons-plus-alt',
				'url'         => admin_url( 'admin.php?page=wikipress&group=user-management&tab=user-registration' ),
				'priority'    => 40,
			),
			array(
				'title'       => __( 'Profile', 'wikipress' ),
				'description' => __( 'Review the frontend account profile experience and user details management.', 'wikipress' ),
				'icon'        => 'dashicons-id',
				'url'         => admin_url( 'admin.php?page=wikipress&group=user-management&tab=user-profile' ),
				'priority'    => 50,
			),
			array(
				'title'       => __( 'Access settings', 'wikipress' ),
				'description' => __( 'Update portal-wide security, access restrictions, and default member controls.', 'wikipress' ),
				'icon'        => 'dashicons-lock',
				'url'         => admin_url( 'admin.php?page=wikipress&group=settings&tab=access' ),
				'priority'    => 60,
			),
		);

		usort(
			$cards,
			static fn( array $left, array $right ): int => (int) ( $left['priority'] ?? 100 ) <=> (int) ( $right['priority'] ?? 100 )
		);
		?>
		<section aria-labelledby="wikipress-dashboard-cards">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h2 id="wikipress-dashboard-cards" class="h5 mb-0"><?php esc_html_e( 'Quick actions', 'wikipress' ); ?></h2>
				<span class="small text-secondary"><?php esc_html_e( 'Manage the core access experience', 'wikipress' ); ?></span>
			</div>
			<div class="row g-3">
				<?php foreach ( $cards as $card ) : ?>
					<div class="col-md-6 col-xl-4">
						<a class="wikipress-summary-card h-100 d-flex flex-column gap-1" href="<?php echo esc_url( $card['url'] ?? '' ); ?>">
							<span class="wikipress-summary-icon dashicons <?php echo esc_attr( $card['icon'] ?? 'dashicons-admin-generic' ); ?>" aria-hidden="true"></span>
							<span class="fw-semibold text-body"><?php echo esc_html( $card['title'] ?? __( 'Action', 'wikipress' ) ); ?></span>
							<span class="small text-secondary"><?php echo esc_html( $card['description'] ?? '' ); ?></span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}


<?php
/**
 * DashboardManager class for the WikiPress plugin.
 *
 * @package WikiPress
 */
namespace WikiPress\Admin\Manager\Dashboard;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Assets\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardManager extends Manager {
	/**
	 * The slug for the dashboard page.
	 *
	 * @var string
	 */
	protected string $page;

	/**
	 * Constructor for the DashboardManager class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->page = 'wikipress';
	}

	/**
	 * Render the dashboard page via the main dashboard UI class.
	 *
	 * @since 1.0.0
	 */
	public function render(): void {
		( new DashboardMain() )->render_page_content();
	}

	/**
	 * Register the assets required for the dashboard page.
	 *
	 * @param Assets $assets The assets manager instance.
	 * @since 1.0.0
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'wikipress' ), 'dashboard' );
	}
}






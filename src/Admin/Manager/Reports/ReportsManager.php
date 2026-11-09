<?php
/**
 * Reports admin page manager.
 *
 * @package WikiPress\Admin\Manager\Reports
 */
namespace WikiPress\Admin\Manager\Reports;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Assets\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ReportsManager extends Manager {
	/**
	 * Render the reports page through the section dashboard UI class.
	 *
	 * @return void
	 */
	public function render(): void {
		( new ReportsDashboard() )->render_page_content();
	}

	/**
	 * Register the assets required for the reports page.
	 *
	 * @param Assets $assets The assets manager instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'wikipress-reports' ), 'reports' );
	}
}



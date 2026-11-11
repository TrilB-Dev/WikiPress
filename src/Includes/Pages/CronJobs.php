<?php
/**
 * CronJobs class for registering plugin cron jobs.
 *
 * @package WikiPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace WikiPress\Includes\Pages;

use WikiPress\Includes\Core\CronJobs as CronJobCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CronJobs {
	/**
	 * Register all page-layer cron jobs for the plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::wikipress_cleanup();
	}

	/**
	 * Register the built-in WikiPress cleanup cron job.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function wikipress_cleanup(): bool {
		return CronJobCore::create(
			'core',
			'wikipress_cleanup',
			'daily',
			array(),
			array( self::class, 'cleanup' ),
			__( 'Daily WikiPress cleanup task.', 'wikipress' )
		);
	}

	/**
	 * Example built-in cron callback.
	 *
	 * @return void
	 */
	public static function cleanup(): void {
		do_action( 'wikipress_daily_cleanup' );
	}
}


<?php
/**
 * Safe maintenance operations for WikiPress.
 *
 * @package WikiPress
 * @subpackage Includes\Tools
 * @since 1.0.0
 */

namespace WikiPress\Includes\Tools;

use WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Maintenance {
	/**
	 * Flush the rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True on success.
	 */
	public static function flush_rewrites(): bool {
		flush_rewrite_rules();
		return true;
	}

	/**
	 * Clear the cache for a specific group or all groups.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group The cache group to clear. If empty, all cache will be cleared.
	 * @return bool True on success.
	 */
	public static function clear_cache( string $group = '' ): bool {
		if ( '' !== $group && function_exists( 'wp_cache_flush_group' ) ) {
			return (bool) wp_cache_flush_group( SanitizationHelper::key( $group ) );
		}

		return (bool) wp_cache_flush();
	}
	/**
	 * Rebuild the maintenance state by flushing rewrites and clearing the cache.
	 *
	 * @since 1.0.0
	 *
	 * @return array An associative array indicating the success of each operation.
	 */
	public static function rebuild(): array {
		return array(
			'rewrites_flushed' => self::flush_rewrites(),
			'cache_cleared'    => self::clear_cache(),
		);
	}
}




<?php
/**
 * Role registration for WikiPress.
 *
 * @package WikiPress\Includes\Core
 * @since 1.0.0
 */
namespace WikiPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Roles {
	/**
	 * Role identifiers for WikiPress.
	 * 
	 * @since 1.0.0
	 * @var string The role identifier for the manager role.
	 */
	public const MANAGER_ROLE = 'wikipress_manager';
	public const AUTHOR_ROLE = 'wikipress_author';


	/**
	 * Install the default WikiPress roles used by WikiPress.
	 *
	 * @return void
	 */
	public static function install(): void {
		if ( ! function_exists( 'add_role' ) || ! function_exists( 'get_role' ) ) {
			return;
		}

		self::manager_role();
		self::author_role();
	}
	/**
	 * Get the manager role identifier.
	 *
	 * @since 1.0.0
	 * @return string The manager role identifier.
	 */
	public static function manager_role(): string {
		if ( ! get_role( self::MANAGER_ROLE ) ) {
			add_role(
				self::MANAGER_ROLE,
				__( 'Mod Manager', 'wikipress' ),
				array(
					'read'                       => true,
					'add_users'       			 => true,
					'edit_users'        		 => true,
					'create_users'      		 => true,
					'delete_users'  			 => true,
					'wikipress_suspend_users'  	 => true,
					'wikipress_ban_users'  	 	 => true,
				)
			);
		}
		return self::MANAGER_ROLE;
	}
	/**
	 * Get the author role identifier.
	 *
	 * @since 1.0.0
	 * @return string The author role identifier.
	 */
	public static function author_role(): string {
		if ( ! get_role( self::AUTHOR_ROLE ) ) {
			add_role(
				self::AUTHOR_ROLE,
				__( 'Mod Author', 'wikipress' ),
				array(
					'read' => true,
					'wikipress_manager_mods_view' => true,
					'wikipress_manager_mods_edit' => true,
					'wikipress_manager_mods_delete' => true,
					'wikipress_manager_groups_view' => true,
				)
			);
		}
		return self::AUTHOR_ROLE;
	}
}





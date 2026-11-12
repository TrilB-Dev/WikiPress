<?php

namespace WikiPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {
	/**
	 * Capability definitions contributed by WikiPress extensions.
	 *
	 * @var array<string, array{group: string, label: string, description: string}>
	 */
	private static array $extensions = array();

	/**
	 * Return the core and registered extension capability definitions.
	 *
	 * @return array<string, array{group: string, label: string, description: string}>
	 */
	public static function definitions(): array {
		return array_merge(
			array(
				'wikipress_admin_view'                => array(
					'group'       => 'WikiPress',
					'label'       => __( 'View WikiPress Administration', 'wikipress' ),
					'description' => __( 'Allows access to the WikiPress dashboard and admin pages.', 'wikipress' ),
				),
				'wikipress_dashboard_view'            => array(
					'group'       => 'WikiPress',
					'label'       => __( 'View WikiPress Dashboard', 'wikipress' ),
					'description' => __( 'Allows viewing the WikiPress dashboard and summary status.', 'wikipress' ),
				),
				'wikipress_settings_general_view'     => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'View WikiPress Settings', 'wikipress' ),
					'description' => __( 'Allows viewing the general WikiPress settings.', 'wikipress' ),
				),
				'wikipress_settings_general_edit'     => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'Edit WikiPress Settings', 'wikipress' ),
					'description' => __( 'Allows editing the general WikiPress settings.', 'wikipress' ),
				),
				'wikipress_settings_layout_view'      => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'View Layout Settings', 'wikipress' ),
					'description' => __( 'Allows viewing WikiPress layout and presentation settings.', 'wikipress' ),
				),
				'wikipress_settings_layout_edit'      => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'Edit Layout Settings', 'wikipress' ),
					'description' => __( 'Allows changing WikiPress layout and presentation settings.', 'wikipress' ),
				),
				'wikipress_settings_access_view'      => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'View Access Controls', 'wikipress' ),
					'description' => __( 'Allows viewing who can do what inside WikiPress.', 'wikipress' ),
				),
				'wikipress_settings_access_edit'      => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'Edit Access Controls', 'wikipress' ),
					'description' => __( 'Allows changing WikiPress access roles and permission boundaries.', 'wikipress' ),
				),
				'wikipress_tools_general'              => array(
					'group'       => 'WikiPress Tools',
					'label'       => __( 'View General Tools', 'wikipress' ),
					'description' => __( 'Allows viewing general tools and settings in WikiPress.', 'wikipress' ),
				),
				'wikipress_tools_import'              => array(
					'group'       => 'WikiPress Tools',
					'label'       => __( 'Import Licence Data', 'wikipress' ),
					'description' => __( 'Allows importing licence exports into the system securely.', 'wikipress' ),
				),
				'wikipress_tools_export'              => array(
					'group'       => 'WikiPress Tools',
					'label'       => __( 'Export Licence Data', 'wikipress' ),
					'description' => __( 'Allows exporting WikiPress records using encryption and a password.', 'wikipress' ),
				),
				'wikipress_tools_debug'               => array(
					'group'       => 'WikiPress Tools',
					'label'       => __( 'View Debug Tools', 'wikipress' ),
					'description' => __( 'Allows using WikiPress debug and diagnostics tools.', 'wikipress' ),
				),
				'wikipress_tools_reset'               => array(
					'group'       => 'WikiPress Tools',
					'label'       => __( 'Reset Licence Data', 'wikipress' ),
					'description' => __( 'Allows resetting or clearing WikiPress records and related data.', 'wikipress' ),
				),
				'wikipress_settings_plugins_view'     => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'View Plugin Settings', 'wikipress' ),
					'description' => __( 'Allows viewing WikiPress plugin settings.', 'wikipress' ),
				),
				'wikipress_settings_plugins_int_view' => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'View Internal Plugin Settings', 'wikipress' ),
					'description' => __( 'Allows viewing settings for internal WikiPress plugins.', 'wikipress' ),
				),
				'wikipress_settings_plugins_int_edit' => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'Edit Internal Plugin Settings', 'wikipress' ),
					'description' => __( 'Allows editing settings for internal WikiPress plugins.', 'wikipress' ),
				),
				'wikipress_settings_plugins_ext_view' => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'View External Plugin Settings', 'wikipress' ),
					'description' => __( 'Allows viewing settings for external WikiPress plugins.', 'wikipress' ),
				),
				'wikipress_settings_plugins_ext_edit' => array(
					'group'       => 'WikiPress Settings',
					'label'       => __( 'Edit External Plugin Settings', 'wikipress' ),
					'description' => __( 'Allows editing settings for external WikiPress plugins.', 'wikipress' ),
				),
				'wikipress_manager_dashboard' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'View Wiki Manager Dashboard', 'wikipress' ),
					'description' => __( 'Allows viewing the WikiPress Wiki Manager Dashboard.', 'wikipress' ),
				),
				'wikipress_manager_wiki_view' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'View Wikis', 'wikipress' ),
					'description' => __( 'Allows viewing wikis within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_wiki_edit' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'Edit Wikis', 'wikipress' ),
					'description' => __( 'Allows editing wikis within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_wiki_edit_others' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'Edit Others\' Wikis', 'wikipress' ),
					'description' => __( 'Allows editing wikis created by other users within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_wiki_page_view' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'View Wiki Pages', 'wikipress' ),
					'description' => __( 'Allows viewing wiki pages within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_wiki_page_edit' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'Edit Wiki Pages', 'wikipress' ),
					'description' => __( 'Allows editing wiki pages within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_wiki_page_edit_others' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'Edit Others\' Wiki Pages', 'wikipress' ),
					'description' => __( 'Allows editing wiki pages created by other users within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_categories_view' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'View Categories', 'wikipress' ),
					'description' => __( 'Allows viewing categories within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_categories_edit' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'Edit Categories', 'wikipress' ),
					'description' => __( 'Allows editing categories within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_tags_view' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'View Tags', 'wikipress' ),
					'description' => __( 'Allows viewing tags within Wiki Manager.', 'wikipress' ),
				),
				'wikipress_manager_tags_edit' => array(
					'group'       => 'Wiki Manager',
					'label'       => __( 'Edit Tags', 'wikipress' ),
					'description' => __( 'Allows editing tags within Wiki Manager.', 'wikipress' ),
				),
			),
			self::$extensions
		);
	}

	/**
	 * Register definitions contributed by a plugin and install any missing caps.
	 *
	 * @param array<string, array{group: string, label: string, description: string}> $definitions Definitions to add.
	 * @return void
	 */
	public static function extend( array $definitions ): void {
		self::$extensions = array_merge( self::$extensions, $definitions );
		self::install();
	}

	/**
	 * Install missing capabilities without removing administrator customizations.
	 *
	 * @return void
	 */
	public static function install(): void {
		$administrator = get_role( 'administrator' );
		if ( ! $administrator ) {
			return;
		}

		foreach ( array_keys( self::definitions() ) as $capability ) {
			if ( ! $administrator->has_cap( $capability ) ) {
				$administrator->add_cap( $capability );
			}
		}
	}
}








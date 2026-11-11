<?php
/**
 * SettingsManager class.
 *
 * @package WikiPress
 * @subpackage Includes\Settings
 * @since 1.0.0
 */

namespace WikiPress\Includes\Settings;

use WikiPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SettingsManager {
    /**
     * Get the name of the settings table.
     *
     * @return string
     * @since 1.0.0
     */
    public static function table_name(): string {
        return Database::table_name( 'settings' );
    }
    /**
     * Get a setting by its key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get( string $key, $default = null ) {
        foreach ( self::get_all() as $settings ) {
            if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
                return $settings[ $key ];
            }
        }

        return $default;
    }
    /**
     * Set a setting by its key.
     *
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public static function set( string $key, $value ): bool {
        $group = self::group_for_key( $key );
        $settings = self::get_group( $group ) ?? [];
        $settings[ $key ] = $value;
        return self::set_group( $group, $settings );
    }
    /**
     * Delete a setting by its key.
     *
     * @param string $key
     * @return boolean
     */
    public static function delete( string $key ): bool {
        $group = self::group_for_key( $key );
        $settings = self::get_group( $group );
        if ( ! is_array( $settings ) || ! array_key_exists( $key, $settings ) ) {
            return false;
        }
        unset( $settings[ $key ] );
        return self::set_group( $group, $settings );
    }
    /**
     * Check if a setting exists.
     *
     * @param string $key
     * @return bool
     * @since 1.0.0
     */
    public static function has( string $key ): bool {
        foreach ( self::get_all() as $settings ) {
            if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
                return true;
            }
        }

        return false;
    }
    /**
     * Get all settings, including stored and default values.
     *
     * @return array
     * @since 1.0.0
     */
    public static function get_all(): array {
        global $wpdb;
        $rows = $wpdb->get_results( 'SELECT setting_group, setting_value FROM ' . self::table_name(), ARRAY_A );
        $settings = [];

        foreach ( $rows ?: [] as $row ) {
            $group = self::logical_group( $row['setting_group'] );
            $stored = maybe_unserialize( $row['setting_value'] );

            if ( ! is_array( $stored ) ) {
                continue;
            }

            $settings[ $group ] = $stored;
        }

        return $settings;
    }
    /**
     * Get the default settings.
     *
     * @return array
     * @since 1.0.0
     */
    public static function get_group( string $group ): ?array {
        global $wpdb;
        $group = self::normalize_group( $group );
        $value = $wpdb->get_var( $wpdb->prepare( 'SELECT setting_value FROM ' . self::table_name() . ' WHERE setting_group = %s', self::storage_group( $group ) ) );
        $settings = $value === null ? null : maybe_unserialize( $value );

        if ( ! is_array( $settings ) ) {
            return null;
        }

        return $settings;
    }
    /**
     * Set the settings for a specific group.
     *
     * @param string $group
     * @param array $settings
     * @return bool
     * @since 1.0.0
     */
    public static function set_group( string $group, array $settings ): bool {
        global $wpdb;
        return false !== $wpdb->replace( self::table_name(), [
            'setting_group' => self::storage_group( $group ),
            'setting_value' => maybe_serialize( $settings ),
            'autoload' => 'yes',
            'updated_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s' ] );
    }
    /**
     * Get the storage group name for a logical group.
     *
     * @param string $group
     * @return string
     * @since 1.0.0
     */
    private static function storage_group( string $group ): string {
        $group = self::normalize_group( $group );
        return str_starts_with( $group, 'wikipress_' ) ? $group : 'wikipress_' . $group;
    }

    /**
     * Get the logical group name from a storage group name.
     *
     * @param string $group
     * @return string
     * @since 1.0.0
     */
    private static function logical_group( string $group ): string {
        return str_starts_with( $group, 'wikipress_' ) ? substr( $group, 10 ) : $group;
    }
    /**
     * Get the logical group name for a given setting key.
     *
     * @param string $key
     * @return string
     * @since 1.0.0
     */
    private static function group_for_key( string $key ): string {
        $key = sanitize_key( $key );
        if ( isset( self::$registered_keys[ $key ] ) ) {
            return self::$registered_keys[ $key ];
        }
        if ( in_array( $key, [ 'create_mods', 'write_pages', 'view_analytics', 'manage_plugins' ], true ) ) {
            return 'access';
        }
        if ( str_contains( $key, 'layout' ) ) {
            return 'layout';
        }
        if ( str_contains( $key, 'access' ) ) {
            return 'access';
        }
        if ( str_contains( $key, 'tool' ) ) {
            return 'tools';
        }
        return 'general';
    }

    /**
     * Normalize a group name by replacing dashes and spaces with underscores and removing the 'wikipress_' prefix if present.
     *
     * @param string $group
     * @return string
     * @since 1.0.0
     */
    private static function normalize_group( string $group ): string {
        $group = str_replace( '-', '_', sanitize_key( $group ) );
        $group = str_replace( ' ', '_', $group );

        if ( str_starts_with( $group, 'wikipress_' ) ) {
            return preg_replace( '/^wikipress_/', '', $group ) ?: $group;
        }

        return $group;
    }
}




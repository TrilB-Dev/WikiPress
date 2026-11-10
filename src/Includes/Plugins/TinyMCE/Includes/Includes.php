<?php
/**
 * TinyMCE Editor Plugin Includes
 *
 * @package WikiPress
 * @subpackage Plugins\TinyMCE\Includes
 * @since 1.0.0
 */

namespace WikiPress\Includes\Plugins\TinyMCE\Includes;

use WikiPress\Includes\Plugins\TinyMCE\Includes\Settings\Settings;

final class Includes {
	/**
	 * Singleton instance of the Includes class.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;
	/**
	 * Retrieves the settings instance for the TinyMCE plugin.
	 * 
	 * @since 1.0.0
	 * @var Settings The settings instance.
	 */
	private Settings $settings;
	/**
	 * Private constructor to enforce the singleton pattern.
	 * 
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->settings = new Settings();
	}
	/**
	 * Retrieves the singleton instance of the Includes class.
	 * 
	 * @since 1.0.0
	 * @return self The singleton instance.
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	/**
	 * Initializes the TinyMCE plugin includes.
	 * 
	 * @since 1.0.0
	 */
	public function init(): void {
		$this->settings->register();
	}
	/**
	 * Retrieves the settings instance for the TinyMCE plugin.
	 * 
	 * @since 1.0.0
	 * @return Settings The settings instance.
	 */
	public function settings(): Settings {
		return $this->settings;
	}
}




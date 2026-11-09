<?php
/**
 * Language internationalization (i18n) for the TinyMCE plugin.
 *
 * @package WikiPress
 * @subpackage Plugins\TinyMCE\Includes
 * @since 1.0.0
 */
namespace WikiPress\Includes\Plugins\TinyMCE\Includes\Core;

final class I18n {
	/**
	 * Loads the plugin's text domain for translation.
	 */
	public static function load_textdomain(): void {
		load_plugin_textdomain(
			'wikipress',
			false,
			dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/Includes/Plugins/TinyMCE/Language/'
		);
	}
}




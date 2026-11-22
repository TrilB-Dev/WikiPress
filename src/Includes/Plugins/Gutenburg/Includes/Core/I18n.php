<?php
/**
 * Language internationalization (i18n) for the WikiPress plugin.
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\Gutenburg\Includes
 * @since 1.0.0
 * 
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes\Core;

class I18n {
    /**
     * Loads the plugin's text domain for translation.
     */
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'wikipress',
            false,
            dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/Includes/Plugins/Gutenburg/Language/'
        );
    }
}



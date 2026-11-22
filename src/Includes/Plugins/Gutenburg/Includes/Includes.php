<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin Includes
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\Demo\Includes
 * @since 1.0.0
 */

namespace WikiPress\Includes\Plugins\Gutenburg\Includes;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Settings\Settings;
use WikiPress\Includes\Plugins\Gutenburg\Includes\Meta\Meta;
//use WikiPress\Includes\Plugins\Gutenburg\Includes\Blocks\Blocks;

final class Includes {
    private static ?self $instance = null;
    private Settings $settings;

    private function __construct() {
        $this->settings = new Settings();
    }

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    public function init(): void {
        $this->settings->register();
        Meta::register();
        //Blocks::register();
    }

    public function settings(): Settings {
        return $this->settings;
    }
}

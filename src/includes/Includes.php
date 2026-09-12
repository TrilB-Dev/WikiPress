<?php
/**
 * Core includes for WikiPress.
 *
 * @package WikiPress\Includes
 */
namespace WikiPress\Includes;

use WikiPress\Includes\Core\Core;
use WikiPress\Includes\Core\WP\WPLoader;
use WikiPress\Includes\Functions\Helpers\LoggerHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Includes {
    /**
     * Internal singleton instance of the Includes class.
     * 
     * @var self|null Singleton instance.
     */
    private static ?self $instance = null;
    /**
     * Core component for WikiPress.
     * 
     * @var Core Core component.
     */
    private Core $core;
    /**
     * Array of registered extension callbacks.
     * 
     * @var array List of extension callbacks.
     */
    private array $extensions = [];
    /**
     * Flag indicating whether the Includes instance has been initialized.
     * 
     * @var bool Initialization status.
     */
    private bool $initialized = false;
    /**
     * Private constructor to enforce singleton pattern.
     */
    private function __construct() {
        $this->core = new Core();
        LoggerHelper::write_log( 'WikiPress core includes initialized.' );
    }
    /**
     * Get the singleton instance of the Includes class.
     *
     * @return self Singleton instance.
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }
    /**
     * Get the Core component of WikiPress.
     *
     * @return Core Core component.
     */
    public function init(): void {
        if ( $this->initialized ) {
            return;
        }

        $this->core->register();
        foreach ( $this->extensions as $extension ) {
            call_user_func( $extension, $this );
        }
        $this->initialized = true;
    }
    /**
     * Get the Core component of WikiPress.
     *
     * @return Core Core component.
     */
    public function core(): Core {
        return $this->core;
    }

    /**
     * Queue an extension initializer for the shared Includes lifecycle.
     *
     * Extensions registered after initialization are invoked immediately.
     *
     * @param callable $extension Callback receiving this Includes instance.
     * @return self
     */
    public function register_extension( callable $extension ): self {
        if ( $this->initialized ) {
            call_user_func( $extension, $this );
        } else {
            $this->extensions[] = $extension;
        }

        return $this;
    }

    /**
     * Attach Core registration to an external WikiPress loader.
     *
     * @param WPLoader $loader Loader owned by the main runtime or an extension.
     * @param string $hook WordPress action name.
     * @param int $priority Hook priority.
     * @return self
     */
    public function register_hooks( WPLoader $loader, string $hook = 'init', int $priority = 10 ): self {
        $this->core->register_hooks( $loader, $hook, $priority );
        return $this;
    }
    /**
     * Check if the Includes instance has been initialized.
     *
     * @return bool True if initialized, false otherwise.
     */
    public function is_initialized(): bool {
        return $this->initialized;
    }
}

<?php
/**
 * ToolsManager class for WikiPress plugin.
 *
 * @package WikiPress
 */
namespace WikiPress\Admin\Manager\Tools;

use WikiPress\Admin\Manager\Manager;
use WikiPress\Admin\Manager\Tools\Debug;
use WikiPress\Admin\Manager\Tools\Reset;
use WikiPress\Admin\Manager\Tools\Import;
use WikiPress\Admin\Manager\Tools\Export;
use WikiPress\Admin\Manager\Tools\General;
use WikiPress\Admin\Manager\Tools\Licencing;
use WikiPress\Assets\Assets;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\PermissionHelper;


class ToolsManager extends Manager {
	/**
	 * The Page variable.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $page The page variable.
	 */
	protected string $page;

	/**
	 * `Constructor` method for the `ToolsManager` class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( bool $initialize_tools = true ) {
		$this->page = 'tools';
	}
	/**
	 * Renders the tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render(): void {
		$tool = $this->resolve_requested_tool();
		$capabilities = array(
			'debug'  => 'wikipress_tools_debug',
			'reset'  => 'wikipress_tools_reset',
			'import' => 'wikipress_tools_import',
			'export' => 'wikipress_tools_export',
			'general' => 'wikipress_tools_general',
			'licencing' => 'wikipress_tools_licencing',
		);
		if ( ! PermissionHelper::can( $capabilities[ $tool ] ) ) {
			wp_die( esc_html__( 'You are not authorized to access this WikiPress tool.', 'wikipress' ) );
		}
		$this->header( $this->title( $tool ) );
		$screen = $this->resolve_tool_screen( $tool );
		if ( null !== $screen ) {
			$screen->render_page_content();
		}
		$this->footer();
	}

	/**
	 * Resolve the active requested tool from either the legacy `tool` query or the
	 * menu-provided `tab` value.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function resolve_requested_tool(): string {
		$tool = RequestHelper::get_key( 'tool', '' );
		if ( '' === $tool ) {
			$tool = RequestHelper::get_key( 'tab', 'debug' );
		}

		$valid_tools = array( 'debug', 'reset', 'import', 'export', 'general', 'licencing' );
		return in_array( $tool, $valid_tools, true ) ? $tool : 'debug';
	}

	/**
	 * Resolve a tool screen lazily to avoid recursive child manager construction.
	 *
	 * @param string $tool Tool slug.
	 * @return object|null
	 * @since 1.0.0
	 */
	private function resolve_tool_screen( string $tool ): ?object {
		switch ( $tool ) {
			case 'debug':
				return new Debug();
			case 'import':
				return new Import();
			case 'general':
				return new General();
			case 'reset':
				return new Reset();
			case 'export':
				return new Export();
			case 'licencing':
				return new Licencing();
		}
	}
	/**
	 * Registers the assets for the tools page.
	 *
	 * @since 1.0.0
	 * @param Assets $assets The Assets instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'wikipress' ), 'tools' );
	}
	/**
	 * Returns the title for the given tool.
	 *
	 * @since 1.0.0
	 * @param string $tool The tool name.
	 * @return string The title for the tool.
	 */
	private function title( string $tool ): string {
		return array(
			'debug'  => __( 'Debug', 'wikipress' ),
			'reset'  => __( 'Reset', 'wikipress' ),
			'import' => __( 'Import', 'wikipress' ),
			'export' => __( 'Export', 'wikipress' ),
			'general' => __( 'General', 'wikipress' ),
			'licencing' => __( 'Licencing', 'wikipress' ),
		)[ $tool ];
	}
}





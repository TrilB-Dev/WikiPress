<?php
/**
 * Export class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Tools;

use WikiPress\Includes\Functions\Admin\FunctionsExport;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\UrlHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Export extends ToolsManager {
	/**
	 * Export handler instance.
	 *
	 * @var FunctionsExport
	 */
	private FunctionsExport $export_functions;

	/**
	 * Constructor for the Export class.
	 *
	 * @return void
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		parent::__construct( false );
		$this->export_functions = new FunctionsExport();
		( $loader ?? new LoaderHelper() )->register_component(
			$this->export_functions,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_export',
					'callback' => 'export_data',
				),
			)
		)->run();
	}
	/**
	 * Render the export form.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Export WikiPress data', 'wikipress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Download a protected JSON export of WikiPress records, customer meta, and validation data.', 'wikipress' ); ?></p>
				<?php echo wp_kses_post(
					FormFieldHelper::button(
						esc_html__( 'Export WikiPress JSON', 'wikipress' ),
						array(
							'href'  => UrlHelper::admin_action_nonce( 'wikipress_export', 'wikipress_export' ),
							'class' => 'btn-outline-primary',
						)
					)
				); ?>
			</div>
		</div>
		<?php
	}
	/**
	 * Render the export page content.
	 *
	 * @return void
	 */
	public function render(): void {
		?>
		<tr>
			<th scope="row"><?php echo wp_kses_post(
				FormFieldHelper::label(
					'wikipress-export',
					esc_html__( 'Import and export', 'wikipress' ),
					array(
						'description' => __( 'Export or import WikiPress data as a password-protected JSON archive.', 'wikipress' ),
						'tooltip'     => __( 'Exports are protected with a WordPress nonce and should use a password whenever shared with partners.', 'wikipress' ),
					)
				)
			); ?></th>
			<td><?php echo wp_kses_post(
				FormFieldHelper::button(
					esc_html__( 'Export WikiPress JSON', 'wikipress' ),
					array(
						'href'  => UrlHelper::admin_action_nonce( 'wikipress_export', 'wikipress_export' ),
						'class' => 'btn-outline-primary',
					)
				)
			); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo FormFieldHelper::label(
				'wikipress-database-manager',
				esc_html__( 'Database manager', 'wikipress' ),
				array(
					'description' => __( 'WikiPress records are kept in the plugin database tables and managed through the core lifecycle.', 'wikipress' ),
					'tooltip'     => __( 'Manual database changes are not required for normal WikiPress operations.', 'wikipress' ),
				)
			); ?></th>
			<td><?php esc_html_e( 'Managed automatically', 'wikipress' ); ?></td>
		</tr>
		<?php
	}
}





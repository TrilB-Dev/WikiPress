<?php
/**
 * Import class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Tools;

use WikiPress\Includes\Functions\Admin\FunctionsImport;
use WikiPress\Includes\Functions\Helpers\AlertHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\UrlHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Import extends ToolsManager {
	/**
	 * Import handler instance.
	 *
	 * @var FunctionsImport
	 */
	private FunctionsImport $import_functions;

	/**
	 * Render the JSON import form below the tools settings form.
	 *
	 * @return void
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		parent::__construct( false );
		$this->import_functions = new FunctionsImport();
		( $loader ?? new LoaderHelper() )->register_component(
			$this->import_functions,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_import',
					'callback' => 'import_data',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_import_group_defaults',
					'callback' => 'import_group_defaults',
				),
			)
		)->run();
	}
	/**
	 * Render the import page content using the shared manager contract.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$this->render();
	}

	/**
	 * Render the import form.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( '1' === (string) ( $_GET['default_groups_imported'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default WikiPress groups and taxonomy terms were imported successfully.', 'wikipress' ), 'success' );
		}
		if ( '1' === (string) ( $_GET['default_groups_import_failed'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default WikiPress groups could not be imported.', 'wikipress' ), 'error' );
		}
		if ( '1' === (string) ( $_GET['default_file_types_imported'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default WikiPress file types were imported successfully.', 'wikipress' ), 'success' );
		}
		if ( '1' === (string) ( $_GET['default_mod_statuses_imported'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default WikiPress mod statuses were imported successfully.', 'wikipress' ), 'success' );
		}
		?>
		<form
			method="post"
			action="<?php echo \esc_url( UrlHelper::admin_action( 'wikipress_import' ) ); ?>"
			enctype="multipart/form-data"
			class="card wikipress-import-form shadow-sm mt-4"
		>
			<?php echo \wp_kses_post( FormFieldHelper::input( 'action', 'wikipress_import', array( 'type' => 'hidden' ) ) ); ?>
			<?php \wp_nonce_field( 'wikipress_import' ); ?>
			<div class="card-body">
				<?php
				echo \wp_kses_post(
					FormFieldHelper::label(
						'wikipress-import-file',
						\__( 'Import WikiPress JSON', 'wikipress' ),
						array(
							'description'  => \__( 'Select a JSON export containing WikiPress records, customer data, and validation metadata.', 'wikipress' ),
							'tooltip'      => \__( 'Import should use a valid archive and, where required, a password-protected file to preserve security.', 'wikipress' ),
							'tooltip_icon' => 'fa-file-import',
						)
					)
				);
				echo \wp_kses_post(
					FormFieldHelper::input(
						'wikipress_import_file',
						'',
						array(
							'id'       => 'wikipress-import-file',
							'type'     => 'file',
							'class'    => 'mb-3',
							'accept'   => 'application/json,.json',
							'required' => true,
						)
					)
				);
				echo \wp_kses_post(
					FormFieldHelper::button(
						\__( 'Import WikiPress JSON', 'wikipress' ),
						array(
							'type'  => 'submit',
							'class' => 'btn-primary',
						)
					)
				);
				?>
			</div>
		</form>
		<div class="card wikipress-import-form shadow-sm mt-4">
			<form
				method="post"
				action="<?php echo \esc_url( UrlHelper::admin_action( 'wikipress_import_group_defaults' ) ); ?>"
			>
				<?php \wp_nonce_field( 'wikipress_import_group_defaults', 'wikipress_import_group_defaults_nonce' ); ?>
				<input type="hidden" name="action" value="wikipress_import_group_defaults" />
				<input type="hidden" name="import_target" value="wikipress_file_type" />
				<div class="card-body">
					<h3 class="h5 mb-3"><?php \esc_html_e( 'Import File Type Defaults', 'wikipress' ); ?></h3>
					<p class="text-secondary mb-3"><?php \esc_html_e( 'This will clear the existing default file types and rebuild the default file types table from scratch.', 'wikipress' ); ?></p>
					<?php echo \wp_kses_post(
						FormFieldHelper::button(
							\__( 'Import File Types', 'wikipress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-warning',
							)
						)
					); ?>
				</div>
			</form>
		</div>
		<div class="card wikipress-import-form shadow-sm mt-4">
			<form
				method="post"
				action="<?php echo \esc_url( UrlHelper::admin_action( 'wikipress_import_group_defaults' ) ); ?>"
			>
				<?php \wp_nonce_field( 'wikipress_import_group_defaults', 'wikipress_import_group_defaults_nonce' ); ?>
				<input type="hidden" name="action" value="wikipress_import_group_defaults" />
				<input type="hidden" name="import_target" value="wikipress_mod_status" />
				<div class="card-body">
					<h3 class="h5 mb-3"><?php \esc_html_e( 'Import Status Defaults', 'wikipress' ); ?></h3>
					<p class="text-secondary mb-3"><?php \esc_html_e( 'This will clear the existing default status terms and rebuild the default statuses table from scratch.', 'wikipress' ); ?></p>
					<?php echo \wp_kses_post(
						FormFieldHelper::button(
							\__( 'Import Statuses', 'wikipress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-warning',
							)
						)
					); ?>
				</div>
			</form>
		</div> <!-- Close card for Import Statuses -->

		<div class="card wikipress-import-form shadow-sm mt-4">
			<div class="card-body">
				<h3 class="h5 mb-3"><?php \esc_html_e( 'Import Group Defaults', 'wikipress' ); ?></h3>
				<p class="text-secondary mb-3"><?php \esc_html_e( 'This will clear the existing default taxonomy terms and rebuild the default groups table from scratch.', 'wikipress' ); ?></p>
				<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#wikipress-import-default-groups-modal">
					<?php \esc_html_e( 'Import Group Defaults', 'wikipress' ); ?>
				</button>
			</div>
		</div>

		<div class="modal fade" id="wikipress-import-default-groups-modal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<form method="post" action="<?php echo \esc_url( UrlHelper::admin_action( 'wikipress_import_group_defaults' ) ); ?>">
					<?php \wp_nonce_field( 'wikipress_import_group_defaults', 'wikipress_import_group_defaults_nonce' ); ?>
					<input type="hidden" name="action" value="wikipress_import_group_defaults" />
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><?php \esc_html_e( 'Import Group Defaults', 'wikipress' ); ?></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php \esc_attr_e( 'Close', 'wikipress' ); ?>"></button>
						</div>
						<div class="modal-body">
							<p><?php \esc_html_e( 'By importing the group defaults, all WikiPress data entered by the user will be deleted and cannot be undone.', 'wikipress' ); ?></p>
							<?php echo FormFieldHelper::checkbox(
								'confirm_default_reset',
								'1',
								\__( 'I understand this will delete all user-entered data and cannot be undone.', 'wikipress' ),
								array(
									'id' => 'wikipress-confirm-default-reset',
								)
							); ?>
						</div>
						<div class="modal-footer d-flex justify-content-between">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php \esc_html_e( 'Cancel', 'wikipress' ); ?></button>
							<button type="submit" class="btn btn-danger"><?php \esc_html_e( 'Import', 'wikipress' ); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}





<?php
/**
 * General tool settings for WikiPress.
 *
 * @package WikiPress\Admin\Manager\Tools
 */
namespace WikiPress\Admin\Manager\Tools;

use WikiPress\Assets\Assets;
use WikiPress\Includes\Core\Schema;
use WikiPress\Includes\Functions\Helpers\AlertHelper;
use WikiPress\Includes\Functions\Helpers\DBHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\PermissionHelper;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class General extends ToolsManager {
	/**
	 * Register the general tools settings save action.
	 *
	 * @param LoaderHelper|null $loader Optional loader instance.
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		parent::__construct( false );
		( $loader ?? new LoaderHelper() )->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_save_tools_general',
					'callback' => 'handle_save',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_reinstall_tables',
					'callback' => 'handle_reinstall_tables',
				),
			)
		)->run();
	}

	/**
	 * Render the general tools settings page.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		if ( '1' === RequestHelper::get_text( 'settings_saved' ) ) {
			AlertHelper::render_admin_notice( __( 'The general tools settings were saved.', 'wikipress' ), 'success' );
		}
		if ( '1' === RequestHelper::get_text( 'tables_reinstalled' ) ) {
			AlertHelper::render_admin_notice( __( 'The WikiPress tables were reinstalled successfully.', 'wikipress' ), 'success' );
		}
		if ( '1' === RequestHelper::get_text( 'tables_reinstall_failed' ) ) {
			AlertHelper::render_admin_notice( __( 'The WikiPress tables could not be reinstalled.', 'wikipress' ), 'error' );
		}

		$values = array(
			'remove_all_data_on_uninstall' => Settings::get_bool( 'remove_all_data_on_uninstall', false ),
			'uninstall_3rd_party_plugins'  => Settings::get_bool( 'uninstall_3rd_party_plugins', false ),
		);
		?>
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'General tools settings', 'wikipress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Configure the default cleanup behaviour used when WikiPress is uninstalled or deactivated.', 'wikipress' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php echo FormFieldHelper::input( 'action', 'wikipress_save_tools_general', array( 'type' => 'hidden' ) ); ?>
					<?php wp_nonce_field( 'wikipress_tools_general', 'wikipress_tools_general_nonce' ); ?>
					<div class="mt-3">
						<?php echo FormFieldHelper::checkbox(
							'wikipress_tools[remove_all_data_on_uninstall]',
							'1',
							__( 'Remove all WikiPress data on uninstall', 'wikipress' ),
							array(
								'id'      => 'wikipress-remove-all-data-on-uninstall',
								'checked' => ! empty( $values['remove_all_data_on_uninstall'] ),
							)
						); ?>
					</div>
					<div class="mt-3">
						<?php echo FormFieldHelper::checkbox(
							'wikipress_tools[uninstall_3rd_party_plugins]',
							'1',
							__( 'Remove 3rd party plugins on uninstall', 'wikipress' ),
							array(
								'id'      => 'wikipress-uninstall-3rd-party-plugins',
								'checked' => ! empty( $values['uninstall_3rd_party_plugins'] ),
							)
						); ?>
					</div>
					<div class="mt-4">
						<?php echo FormFieldHelper::button(
							__( 'Save settings', 'wikipress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-primary',
							)
						); ?>
					</div>
				</form>
			</div>
		</div>

		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Database repair', 'wikipress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Recreate the WikiPress database tables from the registered schema when you need to repair a broken install.', 'wikipress' ); ?></p>
				<div class="d-flex flex-wrap gap-2">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php echo FormFieldHelper::input( 'action', 'wikipress_reinstall_tables', array( 'type' => 'hidden' ) ); ?>
						<?php echo FormFieldHelper::input( 'mode', 'missing', array( 'type' => 'hidden' ) ); ?>
						<?php wp_nonce_field( 'wikipress_reinstall_tables', 'wikipress_reinstall_tables_nonce' ); ?>
						<?php echo FormFieldHelper::button(
							__( 'Create missing tables', 'wikipress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-secondary',
							)
						); ?>
					</form>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php echo FormFieldHelper::input( 'action', 'wikipress_reinstall_tables', array( 'type' => 'hidden' ) ); ?>
						<?php echo FormFieldHelper::input( 'mode', 'full', array( 'type' => 'hidden' ) ); ?>
						<?php wp_nonce_field( 'wikipress_reinstall_tables', 'wikipress_reinstall_tables_nonce' ); ?>
						<?php echo FormFieldHelper::button(
							__( 'Reinstall tables', 'wikipress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-danger',
							)
						); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the general tools settings.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		if ( ! PermissionHelper::can( 'wikipress_tools_general' ) ) {
			wp_die( esc_html__( 'You are not authorized to save these WikiPress tools settings.', 'wikipress' ) );
		}

		check_admin_referer( 'wikipress_tools_general', 'wikipress_tools_general_nonce' );

		$input = isset( $_POST['wikipress_tools'] ) && is_array( $_POST['wikipress_tools'] ) ? wp_unslash( $_POST['wikipress_tools'] ) : array();
		$input = array(
			'remove_all_data_on_uninstall' => ! empty( $input['remove_all_data_on_uninstall'] ),
			'uninstall_3rd_party_plugins'  => ! empty( $input['uninstall_3rd_party_plugins'] ),
		);

		foreach ( $input as $key => $value ) {
			Settings::set( $key, $value );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=wikipress&group=tools&tab=general&settings_saved=1' ) );
		exit;
	}

	/**
	 * Recreate the registered WikiPress database tables.
	 *
	 * @return void
	 */
	public function handle_reinstall_tables(): void {
		if ( ! PermissionHelper::can( 'wikipress_tools_general' ) ) {
			wp_die( esc_html__( 'You are not authorized to reinstall the WikiPress tables.', 'wikipress' ) );
		}

		check_admin_referer( 'wikipress_reinstall_tables', 'wikipress_reinstall_tables_nonce' );

		$mode = RequestHelper::get_text( 'mode' );
		$mode = in_array( $mode, array( 'missing', 'full' ), true ) ? $mode : 'missing';

		$success = $this->reinstall_tables( 'full' === $mode );
		wp_safe_redirect( admin_url( 'admin.php?page=wikipress&group=tools&tab=general&' . ( $success ? 'tables_reinstalled=1' : 'tables_reinstall_failed=1' ) ) );
		exit;
	}

	/**
	 * Recreate the WikiPress schema tables.
	 *
	 * @param bool $full_reinstall Whether to drop all WikiPress tables before recreating them.
	 * @return bool
	 */
	private function reinstall_tables( bool $full_reinstall = false ): bool {
		Schema::register_tables();
		$tables = array();

		foreach ( array_merge( DBHelper::get_core_tables(), DBHelper::get_plugin_tables() ) as $table => $schema ) {
			if ( is_string( $table ) && '' !== trim( $table ) ) {
				$tables[] = $table;
			}
		}
		$tables = array_values( array_unique( $tables ) );

		if ( $full_reinstall ) {
			foreach ( $tables as $table ) {
				DBHelper::drop_table( $table );
			}
		}

		foreach ( $tables as $table ) {
			DBHelper::create_table( $table );
		}

		return ! empty( $tables );
	}

	/**
	 * Backward-compatible render signature used by the settings UI.
	 *
	 * @param array<string, mixed> $values Current settings values.
	 * @return void
	 */
	public function render( array $values = array() ): void {
		$values = array(
			'remove_all_data_on_uninstall' => ! empty( $values['remove_all_data_on_uninstall'] ) || Settings::get_bool( 'remove_all_data_on_uninstall', false ),
			'uninstall_3rd_party_plugins'  => ! empty( $values['uninstall_3rd_party_plugins'] ) || Settings::get_bool( 'uninstall_3rd_party_plugins', false ),
		);
		?>
		<tr>
			<th scope="row"><?php echo wp_kses_post( FormFieldHelper::label( 'wikipress-remove-all-data-on-uninstall', __( 'Remove all data on uninstall', 'wikipress' ) ) ); ?></th>
			<td>
				<?php echo wp_kses_post( FormFieldHelper::checkbox(
					'wikipress_tools[remove_all_data_on_uninstall]',
					'1',
					__( 'Remove all WikiPress data when the plugin is uninstalled', 'wikipress' ),
					array(
						'id'      => 'wikipress-remove-all-data-on-uninstall',
						'checked' => ! empty( $values['remove_all_data_on_uninstall'] ),
					)
				) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo wp_kses_post( FormFieldHelper::label( 'wikipress-uninstall-3rd-party-plugins', __( 'Third-party plugins on uninstall', 'wikipress' ) ) ); ?></th>
			<td>
				<?php echo wp_kses_post( FormFieldHelper::checkbox(
					'wikipress_tools[uninstall_3rd_party_plugins]',
					'1',
					__( 'Remove 3rd party plugins when WikiPress is uninstalled', 'wikipress' ),
					array(
						'id'      => 'wikipress-uninstall-3rd-party-plugins',
						'checked' => ! empty( $values['uninstall_3rd_party_plugins'] ),
					)
				) ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Register page assets.
	 *
	 * @param Assets $assets Assets manager instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'wikipress' ), 'tools' );
	}
}




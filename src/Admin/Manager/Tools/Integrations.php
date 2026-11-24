<?php
/**
 * WikiPress Integrations class.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Tools;

use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Helpers\PermissionHelper;
use WikiPress\Includes\Settings\Settings;
use WikiPress\Includes\Functions\Helpers\RequestHelper;
use WikiPress\Includes\Functions\Helpers\AlertHelper;
use WikiPress\Includes\Integrations\Integrations as IntegrationsAccess;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Integrations extends ToolsManager {
	/**
	 * Constructor for the Integrations class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( false );
		( $loader ?? new LoaderHelper() )->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_wikipress_save_tools_integrations',
					'callback' => 'handle_save',
				),
			)
		)->run();
	}
	/**
	 * Render the integrations settings page content.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$this->render();
	}

	/**
	 * Render debug-related settings fields.
	 *
	 * Intentionally accepts an optional values array to stay compatible with the
	 * parent ToolsManager contract while the debug tool renders its own page.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values = array() ): void {
		if ( '1' === RequestHelper::get_text( 'settings_saved' ) ) {
			AlertHelper::render_admin_notice( __( 'The integrations tools settings were saved.', 'wikipress' ), 'success' );
		}

		$values = array(
			'enable_integrations' => Settings::get_bool( 'enable_integrations', false ),
		);
		?>
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'General tools settings', 'wikipress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Configure the default cleanup behaviour used when WikiPress is uninstalled or deactivated.', 'wikipress' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php echo FormFieldHelper::input( 'action', 'wikipress_save_tools_integrations', array( 'type' => 'hidden' ) ); ?>
					<?php wp_nonce_field( 'wikipress_tools_integrations', 'wikipress_tools_integrations_nonce' ); ?>
					<div class="mt-3">
						<?php echo FormFieldHelper::switch(
							'wikipress_tools[enable_integrations]',
							true,
							__( 'Enable integrations', 'wikipress' ),
							array(
								'id'      => 'wikipress-enable-integrations',
								'checked' => ! empty( $values['enable_integrations'] ) ?? false,
							)
						); ?>
					</div>
					<?php if ( $values['enable_integrations'] ) : ?>
						<?php foreach ( IntegrationsAccess::get_instance()->get_available_definitions() as $integration ) : ?>
							<div class="mt-3">
								<?php echo FormFieldHelper::switch(
									'wikipress_tools[' . esc_attr( $integration['setting_key'] ) . ']',
									true,
									sprintf( __( 'Enable %s integration', 'wikipress' ), $integration['name'] ),
									array(
										'id'      => 'wikipress-enable-' . esc_attr( $integration['slug'] ) . '-integration',
										'checked' => ! empty( $integration['enabled'] ),
									)
								); ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
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
		<?php
	}

    /**
	 * Save the general tools settings.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		if ( ! PermissionHelper::can( 'wikipress_tools_integrations' ) ) {
			wp_die( esc_html__( 'You are not authorized to save these WikiPress tools settings.', 'wikipress' ) );
		}

		check_admin_referer( 'wikipress_tools_integrations', 'wikipress_tools_integrations_nonce' );

		$input = isset( $_POST['wikipress_tools'] ) && is_array( $_POST['wikipress_tools'] ) ? wp_unslash( $_POST['wikipress_tools'] ) : array();
		$input['enable_integrations'] = ! empty( $input['enable_integrations'] );

		foreach ( $input as $key => $value ) {
			Settings::set( $key, $value );
		}

		foreach ( IntegrationsAccess::get_instance()->get_available_definitions() as $integration ) {
			$setting_key = $integration['setting_key'];
			$enabled = ! empty( $input[$setting_key] );
			IntegrationsAccess::get_instance()->set_enabled( $integration['slug'], $enabled );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=wikipress&group=tools&tab=integrations&settings_saved=1' ) );
		exit;
	}
}



<?php
/**
 * Settings access restriction fields.
 *
 * @package TrilBDev
 * @subpackage Admin\Manager\Settings
 */
namespace WikiPress\Admin\Manager\Settings;

use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsAccess {
	/**
	 * Render access restriction fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values ): void {
		$can_edit = current_user_can( 'manage_options' ) || current_user_can( 'wikipress_settings_access_edit' );
		$options = [
			[ 'value' => 'manage_options', 'label' => __( 'Administrators', 'wikipress' ) ],
			[ 'value' => 'edit_posts', 'label' => __( 'Editors', 'wikipress' ) ],
			[ 'value' => 'publish_posts', 'label' => __( 'Authors', 'wikipress' ) ],
		];
		$create_mods = $values['create_mods'] ?? [ 'manage_options' ];
		$write_pages = $values['write_pages'] ?? [ 'manage_options' ];
		$view_analytics = $values['view_analytics'] ?? [ 'manage_options' ];
		$manage_plugins = $values['manage_plugins'] ?? [ 'manage_options' ];
		?>
		<?php if ( $can_edit ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wikipress-settings-form">
			<input type="hidden" name="action" value="wikipress_save_access_settings" />
			<input type="hidden" name="wikipress_tab" value="access" />
			<?php wp_nonce_field( 'wikipress_save_access_settings' ); ?>
		<?php endif; ?>
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'wikipress-access-create-mods',
								__( 'Who can create mods?', 'wikipress' ),
								[
									'description' => __( 'Choose the minimum capability required to create WikiPress mods.', 'wikipress' ),
									'tooltip' => __( 'Users without this capability cannot create new mods.', 'wikipress' ),
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'wikipress_access[create_mods]',
								[
									'id' => 'wikipress-access-create-mods',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $create_mods 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'wikipress-access-write-pages',
								__( 'Who can write mod pages?', 'wikipress' ),
								[
									'description' => __( 'Choose the minimum capability required to create or edit mod pages.', 'wikipress' ),
									'tooltip' => __( 'This controls editing access to mod page content.', 'wikipress' ),
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'wikipress_access[write_pages]',
								[
									'id' => 'wikipress-access-write-pages',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $write_pages 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'wikipress-access-view-analytics',
								__( 'Who can check analytics?', 'wikipress' ),
								[
									'description' => __( 'Choose the minimum capability required to view WikiPress analytics.', 'wikipress' ),
									'tooltip' => __( 'Analytics data is shown only to users who meet this capability.', 'wikipress' ),
									'tooltip_type' => 'info',
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'wikipress_access[view_analytics]',
								[
									'id' => 'wikipress-access-view-analytics',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $view_analytics 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label(
								'wikipress-access-manage-plugins',
								__( 'Who can manage plugins?', 'wikipress' ),
								[
									'description' => __( 'Choose the minimum capability required to manage WikiPress plugins.', 'wikipress' ),
									'tooltip' => __( 'Use a trusted administrator-level capability for plugin management.', 'wikipress' ),
									'tooltip_icon' => 'fa-shield-halved',
								]
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::bootstrap_multiselect(
								'wikipress_access[manage_plugins]',
								[
									'id' => 'wikipress-access-manage-plugins',
									'data' => $options,
									'selected' => array_values( 
										array_filter( 
											array_map( 
												'sanitize_key', 
												(array) $manage_plugins 
											) 
										) 
									),
								]
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php if ( $can_edit ) : ?>
			<div class="wikipress-settings-actions d-flex justify-content-end mt-3">
				<?php echo FormFieldHelper::button(
					__( 'Save Changes', 'wikipress' ),
					[
						'type' => 'submit',
						'name' => 'submit',
						'class' => 'btn-primary',
					]
				); ?>
			</div>
		</form>
		<?php endif; ?>
		<?php
	}
}



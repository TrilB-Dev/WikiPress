<?php
/**
 * Settings general fields.
 * @package WikiPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Settings;

use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\PermalinkHelper;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsGeneral {
	/**
	 * Render general WikiPress settings fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 * @since 1.0.0
	 */
	public function render( array $values ): void {
		$edit_capabilities = array(
			'general' => 'wikipress_settings_general_edit',
		);
		$can_edit = isset( $edit_capabilities[ 'general' ] ) && current_user_can( $edit_capabilities[ 'general' ] );
		$root_name = isset( $values['root_name'] ) ? SanitizationHelper::text( (string) $values['root_name'] ) : '';
		$root_description = isset( $values['root_description'] ) ? SanitizationHelper::text( (string) $values['root_description'] ) : '';
		$archive_title = isset( $values['archive_title'] ) ? SanitizationHelper::text( (string) $values['archive_title'] ) : '';
		$archive_description = isset( $values['archive_description'] ) ? SanitizationHelper::text( (string) $values['archive_description'] ) : '';
		$root_slug = isset( $values['root_slug'] ) ? SanitizationHelper::key( (string) $values['root_slug'] ) : '';
		$category_slug = isset( $values['category_slug'] ) ? SanitizationHelper::key( (string) $values['category_slug'] ) : '';
		$tag_slug = isset( $values['tag_slug'] ) ? SanitizationHelper::key( (string) $values['tag_slug'] ) : '';
		$permalink = isset( $values['permalink'] ) ? PermalinkHelper::sanitize_pattern( (string) $values['permalink'] ) : '';
		$token_definitions = array(
			'%mod_root%'   => __( 'The root of WikiPress.', 'wikipress' ),
			'%mod_type%'   => __( 'The Mod type taxonomy.', 'wikipress' ),
			'%mod_tags%'   => __( 'The Mod tags taxonomy.', 'wikipress' ),
			'%mod_groups%' => __( 'The custom Mod taxonomies.', 'wikipress' ),
			'%mod_post%'   => __( 'The Mod post type.', 'wikipress' ),
		);
		$enable_schema = ! empty( $values['enable_schema'] ?? true );
		?>
		<?php if ( $can_edit ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wikipress-settings-form">
			<input type="hidden" name="action" value="wikipress_save_general_settings" />
			<input type="hidden" name="wikipress_tab" value="general" />
			<?php wp_nonce_field( 'wikipress_save_general_settings' ); ?>
		<?php endif; ?>
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-root-name', 
								__( 'WikiPress Root Name', 'wikipress' ), 
								[
									'description' => __( 'The name used for the main WikiPress area.', 'wikipress' ),
									'tooltip' => __( 'This name appears in the admin interface and generated titles.', 'wikipress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'wikipress_general[root_name]', 
								$root_name, 
								[ 
									'id' => 'wikipress-root-name',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-root-description', 
								__( 'WikiPress Description', 'wikipress' ), 
								[
									'description' => __( 'A short description for the WikiPress knowledge base.', 'wikipress' ),
									'tooltip' => __( 'This can be used by themes and integrations when describing the WikiPress area.', 'wikipress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::textarea( 
								'wikipress_general[root_description]', 
								$root_description, 
								[ 
									'id' => 'wikipress-root-description', 
									'rows' => 3,
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-archive-title', 
								__( 'WikiPress Archive Title', 'wikipress' ), 
								[
									'description' => __( 'The title shown on WikiPress archive and index views.', 'wikipress' ),
									'tooltip' => __( 'Use a concise title that makes the documentation area clear to visitors.', 'wikipress' ),
								] ); ?></th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'wikipress_general[archive_title]', 
								$archive_title, 
								[ 
									'id' => 'wikipress-archive-title',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-archive-description', 
								__( 'WikiPress Archive Description', 'wikipress' ), 
								[
									'description' => __( 'Supporting text shown on WikiPress archive and index views.', 'wikipress' ),
									'tooltip' => __( 'A short introduction helps visitors understand what they can find in the WikiPress area.', 'wikipress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::textarea( 
								'wikipress_general[archive_description]', 
								$archive_description, 
								[ 
									'id' => 'wikipress-archive-description', 
									'rows' => 3,
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-root-slug', 
								__( 'WikiPress Root Slug', 'wikipress' ), 
								[
									'description' => __( 'The URL slug for the WikiPress root.', 'wikipress' ),
									'tooltip' => __( 'Use lowercase letters, numbers, and hyphens for the most reliable URLs.', 'wikipress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'wikipress_general[root_slug]', 
								$root_slug, 
								[ 
									'id' => 'wikipress-root-slug',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-category-slug', 
								__( 'Custom Category Slug', 'wikipress' ), 
								[
									'description' => __( 'The URL slug used for WikiPress categories.', 'wikipress' ),
									'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'wikipress' ),
									'tooltip_type' => 'info',
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'wikipress_general[category_slug]', 
								$category_slug, 
								[ 
									'id' => 'wikipress-category-slug',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-tag-slug', 
								__( 'Custom Tags Slug', 'wikipress' ), 
								[
									'description' => __( 'The URL slug used for WikiPress tags.', 'wikipress' ),
									'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'wikipress' ),
									'tooltip_type' => 'info',
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'wikipress_general[tag_slug]', 
								$tag_slug, 
								[ 
									'id' => 'wikipress-tag-slug',
									'class' => 'w-100',
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-permalink', 
								__( 'WikiPress Permalink', 'wikipress' ), 
								[
									'description' => __( 'The permalink structure used by WikiPress content.', 'wikipress' ),
									'tooltip' => __( 'Choose a structure that remains readable and stable after publication.', 'wikipress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::text_input( 
								'wikipress_general[permalink]', 
								$permalink, 
								[ 
									'id' => 'wikipress-permalink', 
									'data-permalink-field' => 'permalink',
									'class' => 'w-100',
								] 
							); ?>
							<div class="wikipress-permalink-tokens mt-2" aria-label="<?php echo esc_attr__( 'Available permalink tokens', 'wikipress' ); ?>">
								<?php foreach ( $token_definitions as $token => $description ) : ?>
									<?php echo FormFieldHelper::button(
										$token,
										[
											'class' => 'btn-sm btn-outline-secondary me-1 mb-1',
											'type' => 'button',
											'attributes' => [
												'data-permalink-token' => $token,
												'title' => $description,
											],
										]
									); ?>
								<?php endforeach; ?>
							</div>
							<div class="form-text"><?php echo esc_html__( 'Click a token to add it to the pattern. Tokens are inserted with a trailing slash and reappear when removed.', 'wikipress' ); ?></div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'wikipress-enable-schema', 
								__( 'Enable WikiPress Schema', 'wikipress' ), 
								[
									'description' => __( 'Allow themes and integrations to expose WikiPress metadata.', 'wikipress' ),
									'tooltip' => __( 'Keep this enabled when search engines and integrations should understand the WikiPress structure.', 'wikipress' ),
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::checkbox( 
								'wikipress_general[enable_schema]', 
								'1', 
								__( 'Enable WikiPress Schema', 'wikipress' ), 
								[ 
									'id' => 'wikipress-enable-schema', 
									'checked' => $enable_schema 
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



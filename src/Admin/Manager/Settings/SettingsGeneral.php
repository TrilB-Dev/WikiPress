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
	 */
	public function render( array $values ): void {
		$fields = [
			'root_name' => [ 
				'label' => __( 'WikiPress Root Name', 'wikipress' ),
				'description' => __( 'The name used for the main WikiPress area.', 'wikipress' ),
				'tooltip' => __( 'This name appears in the admin interface and generated titles.', 'wikipress' ) 
			],
			'root_description' => [
				'label' => __( 'WikiPress Description', 'wikipress' ),
				'description' => __( 'A short description for the WikiPress knowledge base.', 'wikipress' ),
				'tooltip' => __( 'This can be used by themes and integrations when describing the WikiPress area.', 'wikipress' ),
				'type' => 'textarea'
			],
			'archive_title' => [
				'label' => __( 'Wiki Archive Title', 'wikipress' ),
				'description' => __( 'The title shown on Wiki archive and index views.', 'wikipress' ),
				'tooltip' => __( 'Use a concise title that makes the documentation area clear to visitors.', 'wikipress' )
			],
			'archive_description' => [
				'label' => __( 'Wiki Archive Description', 'wikipress' ),
				'description' => __( 'Supporting text shown on Wiki archive and index views.', 'wikipress' ),
				'tooltip' => __( 'A short introduction helps visitors understand what they can find in the Wiki.', 'wikipress' ),
				'type' => 'textarea'
			],
			'root_slug' => [
				'label' => __( 'WikiPress Root Slug', 'wikipress' ),
				'description' => __( 'The URL slug for the WikiPress root.', 'wikipress' ),
				'tooltip' => __( 'Use lowercase letters, numbers, and hyphens for the most reliable URLs.', 'wikipress' )
			],
			'category_slug' => [
				'label' => __( 'Custom Category Slug', 'wikipress' ),
				'description' => __( 'The URL slug used for WikiPress categories.', 'wikipress' ),
				'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'wikipress' ),
				'tooltip_type' => 'info'
			],
			'tag_slug' => [
				'label' => __( 'Custom Tags Slug', 'wikipress' ),
				'description' => __( 'The URL slug used for WikiPress tags.', 'wikipress' ),
				'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'wikipress' ),
				'tooltip_type' => 'info'
			],
			'permalink' => [
				'label' => __( 'WikiPress Permalink', 'wikipress' ),
				'description' => __( 'The permalink structure used by WikiPress content.', 'wikipress' ),
				'tooltip' => __( 'Choose a structure that remains readable and stable after publication.', 'wikipress' )
			],
			'enable_schema' => [
				'label' => __( 'Enable Documentation Schema', 'wikipress' ),
				'description' => __( 'Allow WikiPress themes and integrations to expose documentation metadata.', 'wikipress' ),
				'tooltip' => __( 'Keep this enabled when search engines and integrations should understand the Wiki structure.', 'wikipress' ),
				'type' => 'checkbox',
				'default' => true
			],
		];
		foreach ( $fields as $key => $field ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-' . $key;
			$name = 'wikipress_general[' . $key . ']';
			$value = 'permalink' === $key ? PermalinkHelper::sanitize_pattern( $values[ $key ] ?? '' ) : SanitizationHelper::text( $values[ $key ] ?? $field['default'] ?? '' );
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $field['label'], $field ) . '</th><td>';
			if ( 'textarea' === ( $field['type'] ?? '' ) ) {
				echo FormFieldHelper::textarea( $name, $value, [ 'id' => $id, 'rows' => 3 ] );
			} elseif ( 'checkbox' === ( $field['type'] ?? '' ) ) {
				echo FormFieldHelper::checkbox( $name, '1', $field['label'], [ 'id' => $id, 'checked' => ! empty( $values[ $key ] ?? $field['default'] ) ] );
			} else {
				echo FormFieldHelper::text_input( $name, $value, [ 'id' => $id, 'data-permalink-field' => 'permalink' === $key ? 'permalink' : null ] );
			}
			if ( 'permalink' === $key ) {
				echo '<div class="wikipress-permalink-tokens mt-2" aria-label="' . esc_attr__( 'Available permalink tokens', 'wikipress' ) . '">';
				foreach ( PermalinkHelper::token_definitions() as $token => $description ) {
					echo FormFieldHelper::button( $token, [
						'class' => 'btn-sm btn-outline-secondary me-1 mb-1',
						'type' => 'button',
						'attributes' => [
							'data-permalink-token' => $token,
							'title' => $description,
						],
					] );
				}
				echo '</div><div class="form-text">' . esc_html__( 'Click a token to add it to the pattern. Tokens are inserted with a trailing slash and reappear when removed.', 'wikipress' ) . '</div>';
			}
			echo '</td></tr>';
		}
	}
}

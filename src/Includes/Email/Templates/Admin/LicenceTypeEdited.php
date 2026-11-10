<?php
/**
 * Admin email template for edited licence types.
 *
 * @package WikiPress
 */

namespace WikiPress\Includes\Email\Templates\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LicenceTypeEdited extends EmailTemplate {
	/** @var string */
	protected string $template_name = 'LicenceTypeEdited';

	public function render( array $context = array() ): string {
		$values      = array_merge( $this->context, $context );
		$name        = $this->text( $values['name'] ?? __( 'Licence type', 'wikipress' ) );
		$description = $this->text( $values['description'] ?? '' );

		$html = '<h2 style="margin: 0 0 12px;">' . esc_html__( 'Licence type updated', 'wikipress' ) . '</h2>
            <p style="margin: 0 0 12px;">' . esc_html__( 'A WikiPress licence type has been edited.', 'wikipress' ) . '</p>
            <p style="margin: 0 0 12px;"><strong>' . esc_html__( 'Name', 'wikipress' ) . ':</strong> ' . esc_html( $name ) . '</p>
            <p style="margin: 0;">' . esc_html( $description ) . '</p>';

		return $this->wrap( $html );
	}
}




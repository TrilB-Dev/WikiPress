<?php
/**
 * Admin email template for retired licence types.
 *
 * @package WikiPress
 */

namespace WikiPress\Includes\Email\Templates\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class LicenceTypeRetired extends EmailTemplate {
	/** @var string */
	protected string $template_name = 'LicenceTypeRetired';

	public function render( array $context = array() ): string {
		$values = array_merge( $this->context, $context );
		$name   = $this->text( $values['name'] ?? __( 'Licence type', 'wikipress' ) );
		if ( ! empty( $values['retired'] ) ) {
			$message = __( 'retired', 'wikipress' );
		} else {
			$message = __( 'reactivated', 'wikipress' );
		}

		/* translators: %s is the licence type status label. */
		$status_message = sprintf( __( 'This licence type has been %s.', 'wikipress' ), $message );

		$html = '<h2 style="margin: 0 0 12px;">' . esc_html__( 'Licence type status changed', 'wikipress' ) . '</h2>
            <p style="margin: 0 0 12px;">' . esc_html( $status_message ) . '</p>
            <p style="margin: 0;"><strong>' . esc_html__( 'Name', 'wikipress' ) . ':</strong> ' . esc_html( $name ) . '</p>';

		return $this->wrap( $html );
	}
}




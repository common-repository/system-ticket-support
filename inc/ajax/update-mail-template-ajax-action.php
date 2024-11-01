<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_update_subject_mail_nonce_field'] )
     && wp_verify_nonce( $_POST['sts_update_subject_mail_nonce_field'], 'processing-update-subject' )
) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		$subject      = sanitize_text_field( stripslashes( $_POST['subject_value'] ) );
		$content      = wpautop( stripslashes( wp_filter_post_kses( $_POST['mailContent'] ) ) );
		$id           = absint( sanitize_text_field( $_POST['id'] ) );
		if ( $id === 0 ) {
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		if ( $subject == '' || $content == '' ) {
			$error = esc_html__( 'Please fill in all field!!!!', 'sts' );
		} else {
			$table_name = $table_prefix . 'mail_template';
			$result     = $wpdb->update(
				$table_name,
				array(
					'subject' => $subject,
					'content' => $content,
				),
				array( 'id' => $id )
			);
			if ( $result === false ) {
				$error = esc_html__( 'Can not save the subject!!!', 'sts' );
			}
		}
	} else {
		$error = esc_html__( "You have not permission to update mail template", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		sts_support_page_url( 'mail-templates' ),
		'redirect' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
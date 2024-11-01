<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_edit_message_security', 'nonce' ) ) {
	$message_id = absint( sanitize_text_field( $_POST['message_id'] ) );
	$ticket_id  = absint( sanitize_text_field( $_POST['ticket_id'] ) );

	if ( $ticket_id === 0 || $message_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$message = wpautop( stripslashes( sanitize_text_field( $_POST['message'] ) ) );
	if ( $message != '' ) {
		$current_user = wp_get_current_user();
		sts_logout_locked( $current_user->ID );
		$messages    = STS()->db()->messages()->get_all_message( $ticket_id );
		$message_obj = STS()->db()->messages()->get_message_by_id( $message_id );
		if ( is_null( $message_obj ) ) {
			sts_send_json(
				array(
					'target'  => '#sts-message-details',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		if ( ( $current_user->ID == $message_obj->user_id && count( $messages ) > 0 && max( $messages )->id == $message_id ) || user_can( $current_user, 'administrator' )
		) {
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'message';
			$result       = $wpdb->update(
				$table_name,
				array(
					'message' => $message
				),
				array( 'id' => $message_id )
			);
			if ( $result !== false ) {
				ob_start();
				STS()->get_template( 'ticket-details/message-content.php', array( 'message' => $message ) );
				$content = ob_get_clean();
			} else {
				$error = esc_html__( "Can not update message!!", 'sts' );
			}
		} else {
			$error = esc_html__( "You can not edit this message!", 'sts' );
		}
	} else {
		$error = esc_html__( "Please type your message!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'target'        => '#single-ticket__content-' . $message_id,
		'content'       => $content,
		'close_content' => '#message-action__form-' . $message_id,
		'open_content'  => '#button-' . $message_id
	), 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-details',
			'message' => $error
		),
		'alert' );
}
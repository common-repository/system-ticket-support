<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_get_message_security', 'nonce' ) ) {
	$message_id = absint( sanitize_text_field( $_POST['id'] ) );
	$ticket_id  = absint( sanitize_text_field( $_POST['ticket_id'] ) );
	if ( $message_id === 0 || $ticket_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$message_obj = STS()->db()->messages()->get_message_by_id( $message_id );
	if ( is_null( $message_obj ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$messages = STS()->db()->messages()->get_all_message( $ticket_id );
	if ( ( $current_user->ID == $message_obj->user_id && max( $messages )->id == $message_id )
	     || user_can( $current_user, 'administrator' ) ) {
		$content = $message_obj->message;
	} else {
		$error = esc_html__( "You can not get this message", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'content'       => $content,
		'target'        => 'editMessage' . $message_id,
		'open_content'  => '#message-action__form-' . $message_id,
		'close_content' => '#button-' . $message_id
	), '' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-details',
			'message' => $error
		),
		'alert' );
}

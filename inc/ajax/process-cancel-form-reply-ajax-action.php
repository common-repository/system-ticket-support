<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_process_cancel_form_reply_security', 'nonce' ) ) {
	$ticket_id = absint( sanitize_text_field( $_POST['ticket_id'] ) );
	if ( $ticket_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-reply-ticket',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	if ( is_null( $ticket ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-reply-ticket',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	global $wpdb;
	if ( ( $current_user->ID == $ticket->customer_id && user_can( $current_user, 'subscriber' ) ) || user_can( $current_user, 'administrator' )
	     || ( ( user_can( $current_user, 'supporter' ) || user_can( $current_user, 'leader_supporter' ) ) && $current_user->ID == $ticket->user_lock ) ) {
		$table_prefix = STS()->db()->table_prefix();
		$table_name   = $table_prefix . 'ticket';
		if ( user_can( $current_user, 'administrator' )
		     || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'leader_supporter' ) ) {
			$error = STS()->db()->tickets()->unlock_ticket( $ticket_id );
		}
	} else {
		$error = esc_html__( "You have not permission to do this action", 'sts' );
	}
} else {
	$error =
		esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'open_content'  => '#post-reply',
		'close_content' => '.single-ticket__action-content,#unlock-reply'
	), 'update' );


} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-reply-ticket',
			'message' => $error
		),
		'alert' );
}

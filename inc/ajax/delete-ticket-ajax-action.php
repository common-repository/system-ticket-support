<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_delete_ticket', 'nonce' )
) {
	$ticket_id = absint( sanitize_text_field( $_POST['id'] ) );
	if ( $ticket_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	if ( is_null( $ticket ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$count_message = STS()->db()->messages()->count_message_by_ticket_id( $ticket_id );
	if ( ( $current_user->ID == $ticket->customer_id && user_can( $current_user, 'subscriber' ) && $count_message < 1 ) || user_can( $current_user, 'administrator' ) ) {
		global $wpdb;
		$table_prefix  = STS()->db()->table_prefix();
		$result_ticket = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_prefix}ticket where id=%d",
			$ticket_id ) );
		if ( $result_ticket !== false && $result_ticket !== 0 ) {
			$messages = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}message WHERE ticket_id=%d",
				$ticket_id
			) );;
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$table_prefix}message where ticket_id=%d",
				$ticket_id ) );
			foreach ( $messages as $message ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$table_prefix}attachment where message_id=%d",
					$message->id ) );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$table_prefix}note where ticket_id=%d",
				$ticket_id ) );

		} else {
			$error = esc_html__( 'Can not delete the ticket!!!', 'sts' );
		}
	} else {
		$error = esc_html__( "You have not permission to delete this ticket", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		sts_support_page_url( 'tickets' ),
		'redirect' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-details',
			'message' => $error
		),
		'alert' );
}
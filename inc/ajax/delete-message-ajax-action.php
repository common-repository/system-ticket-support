<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_delete_message', 'nonce' ) ) {
	$message_id = absint( sanitize_text_field( $_POST['id'] ) );
	$ticket_id  = absint( sanitize_text_field( $_POST['custom_id'] ) );
	if ( $message_id === 0 || $ticket_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	if ( is_null( $ticket ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$messages = STS()->db()->messages()->get_all_message( $ticket_id );
	$message  = STS()->db()->messages()->get_message_by_id( $message_id );
	if ( is_null( $message ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-details',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( ( $current_user->ID == $message->user_id && count( $messages ) > 0 && max( $messages )->id == $message_id )
	     || user_can( $current_user, 'administrator' )
	) {
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		$table_name   = $table_prefix . 'message';
		$message_sp   = STS()->db()->messages()->get_supporters_of_message( $ticket_id );
		$nb_message   = 0;
		if ( $message_sp ) {
			$nb_message = STS()->db()->messages()->count_message_by_supporter( $message_sp->user_id, $ticket_id );
		}
		$result = $wpdb->delete( $table_name, array( 'id' => $message_id ) );
		if ( $result !== false && $result !== 0 ) {
			if ( $ticket->status != 3 ) {
				$last_message = STS()->db()->messages()->get_last_message( $ticket_id );
				if ( $last_message ) {
					$user = get_user_by( "ID", $last_message->user_id );
					if ( user_can( $user, 'subscriber' ) ) {
						$status = 1;
					} else if ( user_can( $user, 'administrator' ) || user_can( $user, 'supporter' ) || user_can( $user, 'leader_supporter' ) ) {
						$status = 2;
					}
					$wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set status=%d where id=%d", $status, $ticket_id ) );
				}
			}
			if ( user_can( $current_user, 'leader_supporter' )
			     || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'administrator' ) ) {
				if ( $nb_message == 1 && ( ! is_null( $ticket->supporter_id ) ) && $ticket->supporter_id == $current_user->ID ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set status=1, supporter_id=NULL,assigned_date=NULL where id=%d", $ticket_id ) );
				}
			}
			$table_name  = $table_prefix . 'attachment';
			$attachments = STS()->db()->attachments()->get_attachment_by_message( $message_id );
			if ( $attachments ) {
				foreach ( $attachments as $attachment ) {
					$wpdb->delete( $table_name, array( 'id' => $attachment->id ) );
				}
			}
		} else {
			$error = esc_html__( 'Can not delete this message!!!', 'sts' );
		}
	} else {
		$error = esc_html__( "You can not delete this message", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		sts_support_page_url( "/ticket-details?t=" . $ticket_id ),
		'redirect' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-change-message-' . $message_id,
			'message' => $error
		),
		'alert' );
}
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_change_status_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	$ticket_id    = absint( sanitize_text_field( $_POST['ticket_id'] ) );
	$status       = absint( sanitize_text_field( $_POST['status'] ) );
	if ( $ticket_id === 0 || $status === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-change-status',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}

	$ticket = STS()->db()->tickets()->get_ticket_by_id( absint( $ticket_id ) );
	if ( is_null( $ticket ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-change-status',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || ( user_can( $current_user, 'leader_supporter' ) && $current_user->ID == $ticket->supporter_id )
	     || ( user_can( $current_user, 'supporter' ) && $current_user->ID == $ticket->supporter_id ) ) {
		global $wpdb;
		$date          = date( "Y-m-d H:i:s" );
		$customer      = get_user_by( 'ID', $ticket->customer_id );
		$table_prefix  = STS()->db()->table_prefix();
		$sent          = true;
		$latest_update = $date;
		if ( $status == 3 ) {
			STS()->db()->tickets()->close_ticket( $ticket->id );
			$latest_update = $ticket->updating_date;
		} else {
			$close_date = null;
		}
		$history_update = array(
			'updating_date' => $date,
			'supporter_id'  => $ticket->supporter_id,
			'user'          => $current_user->ID,
			'status'        => $status,
			'action'        => 'change status'
		);
		if ( ! is_null( $ticket->history ) && $ticket->history != '' ) {
			$history   = unserialize( $ticket->history );
			$history[] = $history_update;
		} else {
			$history = $history_update;
		}

		$result = $wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set status=%d,updating_date=%s,history=%s where id=%d",
			$status, $date, serialize( $history ), $ticket_id ) );
		if ( $result !== false ) {
			if ( $status == 1 && ( is_null( $ticket->supporter_id ) || $ticket->supporter_id == '' ) ) {
				ob_start();
				STS()->get_template( 'ticket-details/mark-process.php', array(
					'ticket_id' => $ticket_id,
					'nonce'     => wp_create_nonce( 'sts_mark_process_security' )
				) );
				$contents = ob_get_clean();

			} else {
				$contents = '';
			}
			if ( $status == 3 ) {
				$status_name    = esc_html__( 'Unclose', 'sts' );
				$status_display = esc_html__( 'Close', 'sts' );
				$status_value   = 1;
				$close_content  = '.form-status,#sts-action-reply,.single-ticket__meta-process';
				$open_content   = '';
			} elseif ( $status == 1 ) {
				$status_name    = esc_html__( 'Close', 'sts' );
				$status_display = esc_html__( 'Request', 'sts' );
				$status_value   = 3;
				$close_content  = '.form-status';
				$open_content   = '#sts-action-reply,.single-ticket__meta-process';
			}
			ob_start();
			STS()->get_template( 'ticket-details/status-option.php', array(
				'status'      => $status_value,
				'status_name' => $status_name
			) );
			$status_content  = ob_get_clean();
			$user_followings = STS()->db()->tickets()->get_user_follow_ticket( $ticket_id );
			$userIDs         = array();
			$userIDs[]       = $ticket->customer_id;
			if ( ! is_null( $ticket->supporter_id ) ) {
				$userIDs[] = $ticket->supporter_id;
			}
			$user_notifis = array_unique( array_merge( $user_followings, $userIDs ) );
			$content      = '<strong>' . $current_user->display_name . '</strong> ' . esc_html__( 'has changed status of  ticket', 'sts' ) . ' <strong> ' . $ticket->subject . '</strong> ' . esc_html__( 'to', 'sts' ) . ' <strong>' . $status_display . '</strong>';
			STS()->db()->notification()->sts_send_notification( $content, $user_notifis, $ticket_id );
		} else {
			$error = esc_html__( "Can not update status!!", 'sts' );
		}
	} else {
		$error = esc_html__( "You have not permission to change status", 'sts' );
	}
} else {
	$error =
		esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'target'               => '.single-ticket__meta-process',
		'content'              => $contents,
		'target_status'        => '.ticket-metadata__status',
		'status'               => $status_display,
		'target_form_control'  => '#status-ticket-details',
		'form_control_content' => $status_content,
		'close_content'        => $close_content,
		'open_content'         => $open_content
	), 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-change-status',
			'message' => $error
		),
		'alert' );
}
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_change_supporter_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	$supporter_id = absint( sanitize_text_field( $_POST['supporter'] ) );
	$ticket_id    = absint( sanitize_text_field( $_POST['ticket_id'] ) );
	if ( $ticket_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-change-supporter',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}

	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' ) || user_can( $current_user, 'leader_supporter' ) ) {
		global $wpdb;
		$date         = date( "Y-m-d H:i:s" );
		$table_prefix = STS()->db()->table_prefix();
		$ticket       = STS()->db()->tickets()->get_ticket_by_id( absint( $ticket_id ) );
		if ( is_null( $ticket ) ) {
			sts_send_json(
				array(
					'target'  => '#sts-message-change-supporter',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		$supporter     = get_user_by( 'ID', absint( $supporter_id ) );
		$is_not_assign = false;
		if ( ! $supporter ) {
			$is_not_assign  = true;
			$supporter_name = esc_html__( 'Not assign', 'sts' );
		} else {
			$supporter_name = $supporter->display_name;
		}
		if ( $supporter_id == '' ) {
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set supporter_id=NULL , assigned_date=NULL,updating_date=%s where id=%d",
				$date, $ticket_id ) );
		} else {
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set supporter_id=%d, assigned_date=%s, updating_date=%s where id=%d",
				$supporter_id, $date, $date, $ticket_id ) );
		}

		if ( $result === false ) {
			$error = esc_html__( "Can not update supporter!!", 'sts' );
		} else {
			$userIDs = array();
			if ( ! is_null( $ticket->supporter_id ) ) {
				$userIDs[] = $ticket->supporter_id;
			}
			$userIDs[] = $supporter_id;
			$content   = '<strong>' . $current_user->display_name . '</strong> ' . esc_html__( 'has change supporter of ticket', 'sts' ) . ' <strong> ' . $ticket->subject . '</strong>';
			STS()->db()->notification()->sts_send_notification( $content, $userIDs, $ticket_id );
		}
	} else {
		$error = esc_html__( "You have not permission to assign supporter", 'sts' );
	}
} else {

	$error = esc_html__( "Error", 'sts' );

}
if ( $error == '' ) {
	if ( ! $is_not_assign ) {
		ob_start();
		STS()->get_template( 'tickets/supporter-processing.php', array( 'supporter_name' => $supporter->display_name ) );
		$content_supporter = ob_get_clean();
	} else {
		$content_supporter = '';
	}

	if ( user_can( $current_user, 'administrator' ) ) {
		$close_content = '';
	} else {
		$close_content = ',#sts-action-reply';
	}
	sts_send_json( array(
		'target'        => '.ticket-metadata__supporter',
		'content'       => $supporter_name,
		'close_content' => '.form-supporter' . $close_content
	), 'replace' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-change-supporter',
			'message' => $error
		),
		'alert' );
}
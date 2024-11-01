<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_mark_process_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$ticket_id = absint( sanitize_text_field( $_POST['ticket_id'] ) );
	if ( $ticket_id === 0 ) {
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
	if ( ( user_can( $current_user, 'administrator' )
	       || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'leader_supporter' ) )
	     && ( $ticket->is_lock == 0 || is_null( $ticket->is_lock ) ) ) {

		$date = date( "Y-m-d H:i:s" );
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();

		$history_update = array(
			'updating_date' => $date,
			'supporter_id'  => $current_user->ID,
			'user'          => $current_user->ID,
			'action'        => 'markprocess'
		);
		if ( ! is_null( $ticket->history ) && $ticket->history != '' ) {
			$history   = unserialize( $ticket->history );
			$history[] = $history_update;
		} else {
			$history = $history_update;
		}
		$error  = STS()->db()->tickets()->lock_ticket( $current_user->ID, $ticket_id );
		$result = true;
		if ( $error == '' ) {
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set updating_date=%s,history=%s where id=%d",
				$date, serialize( $history ), $ticket_id ) );
		}

		$nonce = wp_create_nonce( 'sts_unmark_process_security' );
		if ( $result !== false && $result !== 0 ) {
			$supporters = get_users( array( 'role__in' => array( 'leader_supporter' ) ) );
			$user_ids   = array();
			foreach ( $supporters as $supporter ) {
				$user_ids[] = $supporter->ID;
			}
			$content = '<strong>' . $current_user->display_name . '</strong> ' . esc_html__( 'has marked process ticket', 'sts' ) . '<strong> ' . $ticket->subject . '</strong>';
			STS()->db()->notification()->sts_send_notification( $content, $user_ids, $ticket_id );
			ob_start();
			STS()->get_template( 'ticket-details/unmark-process.php', array(
				'ticket_id' => $ticket_id,
				'nonce'     => $nonce
			) );
			$content = ob_get_clean();
		} else {
			$error = esc_html__( "Can not update status!!", 'sts' );
		}
	} else {
		$error = esc_html__( "You have not permission to mark process this ticket", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'target'       => '.single-ticket__meta-process',
		'content'      => $content,
		'open_content' => '#sts-action-reply,#sts-change-status'
	), 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-details',
			'message' => $error
		),
		'alert' );
}
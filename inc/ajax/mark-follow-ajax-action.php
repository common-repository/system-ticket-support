<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_mark_follow_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'leader_supporter' ) ) {
		$ticket_id = absint( sanitize_text_field( $_POST['ticket_id'] ) );
		if ( $ticket_id === 0 ) {
			sts_send_json(
				array(
					'target'  => '#sts-message-details',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		$date = date( "Y-m-d H:i:s" );
		global $wpdb;
		$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
		if ( is_null( $ticket ) ) {
			sts_send_json(
				array(
					'target'  => '#sts-message-details',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		$table_prefix  = STS()->db()->table_prefix();
		$result_ticket = $wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set updating_date=%s where id=%d",
			$date, $ticket_id ) );
		$table_name    = $table_prefix . 'user_follow_ticket';
		$result_follow = $wpdb->insert(
			$table_name,
			array(
				'user_id'   => $current_user->ID,
				'ticket_id' => $ticket_id,
			)
		);
		if ( $result_ticket !== false && $result_ticket !== 0 && $result_follow !== false && $result_follow !== 0 ) {
			ob_start();
			STS()->get_template( 'ticket-details/unmark-follow.php', array(
				'ticket_id' => $ticket_id,
				'nonce'     => wp_create_nonce( 'sts_unfollow_security' )
			) );
			$content = ob_get_clean();

		} else {
			$error = esc_html__( "Can not follow this ticket!!", 'sts' );
		}
	} else {
		$error = esc_html__( "You have not permission to mark follow this ticket!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'target'  => '.single-ticket__meta-follow',
		'content' => $content
	), 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-details',
			'message' => $error
		),
		'alert' );
}

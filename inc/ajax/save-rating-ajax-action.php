<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_submit_rating_field'] ) &&
     wp_verify_nonce( $_POST['sts_submit_rating_field'], 'processing-save-rating' )
) {
	global $wpdb;
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$table_prefix = STS()->db()->table_prefix();
	$rate         = absint( sanitize_text_field( $_POST['rate'] ) );
	$rate_info    = sanitize_text_field( $_POST['rateInfo'] );
	$ticket_id    = absint( sanitize_text_field( $_POST['ticketID'] ) );
	$key          = sanitize_text_field( $_POST['key'] );
	if ( $ticket_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-rating',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$date    = date( "Y-m-d H:i:s" );
	$key_obj = STS()->db()->key_mail_rate()->get_key_by_user_ticket_code( $current_user->ID, $ticket_id, $key );
	$ticket  = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	if ( $key ) {
		if ( $date < date( $key_obj->date_expired ) ) {
			if ( ( $current_user->ID == $ticket->customer_id && $ticket->status == 3 && $key == md5( $key_obj->key_name )
			) ) {
				$error      = '';
				$table_name = $table_prefix . 'ticket';
				$result     = $wpdb->update(
					$table_name,
					array(
						'rate_date' => $date,
						'rate_info' => $rate_info,
						'rate'      => $rate,
					),
					array( 'ID' => $ticket_id )
				);
				if ( $result !== false ) {
					$success_msg = esc_html__( 'Thank you for your appreciation!!', 'sts' );

				} else {
					$error = esc_html__( 'Can not appriciate!!!', 'sts' );
				}
			} else {
				$error = esc_html__( "You have not permission to rate or key has not exist or ticket not close", 'sts' );
			}
		} else {
			$error = esc_html__( "Key has expired!!", 'sts' );
		}
	} else {
		$error = esc_html__( "Key not exist!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		array(
			'target'  => '#sts-message-rating',
			'message' => $success_msg
		),
		'alert-success' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-rating',
			'message' => $error
		),
		'alert' );
}
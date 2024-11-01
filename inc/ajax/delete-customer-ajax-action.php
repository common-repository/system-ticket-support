<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_delete_customer', 'nonce' )
) {
	$current_user = wp_get_current_user();
	$customer_id  = absint( sanitize_text_field( $_POST['id'] ) );
	if ( $customer_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '.form__message',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$supporter_ids = STS()->db()->tickets()->get_supporter_id_by_customer( $customer_id );
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || ( user_can( $current_user, 'supporter' ) && in_array( $current_user->ID, $supporter_ids ) )
	     || ( user_can( $current_user, 'leader_supporter' ) && in_array( $current_user->ID, $supporter_ids ) ) ) {
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		$tickets      = STS()->db()->tickets()->get_ticket_by_customer_all( $customer_id );
		if ( $tickets ) {
			update_user_meta( $customer_id, 'sts_is_lock', 1 );
			$target = '#sts-lock-user-' . $customer_id;
			ob_start();
			STS()->get_template( 'users/unlock-user.php',
				array( 'user_id' => $customer_id ) );
			$content = ob_get_clean();
			$status  = 'update';
		} else {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			$result = wp_delete_user( $customer_id );
			if ( $result ) {
				$target = '#tr-' . $customer_id;
				$status = 'delete';
			} else {
				$error = esc_html__( 'Can not delete this customer!!!', 'sts' );
			}
		}
		$is_last = count( get_users( array( 'role' => 'subscriber' ) ) );
		$message = '';
		if ( $is_last == 0 ) {
			$message = esc_html__( 'No customer found!!', 'sts' );
		}

	} else {
		$error = esc_html__( "You have not permission delete this customer", 'sts' );
	}
} else {
	$error = esc_html__( "Error!!", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'target'  => $target,
		'is_last' => $is_last,
		'message' => $message,
		'content' => $content
	), $status );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
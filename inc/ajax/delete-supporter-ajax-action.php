<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_delete_supporter', 'nonce' )
) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
		$supporter_id = absint( sanitize_text_field( $_POST['id'] ) );
		if ( $supporter_id === 0 ) {
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		$table_prefix = STS()->db()->table_prefix();
		global $wpdb;
		$tickets = STS()->db()->tickets()->get_ticket_by_supporter( $supporter_id, '', 0 );
		$content = '';
		if ( $tickets ) {
			update_user_meta( $supporter_id, 'sts_is_lock', 1 );
			$target = '#sts-lock-user-' . $supporter_id;
			ob_start();
			STS()->get_template( 'users/unlock-user.php',
				array( 'user_id' => $supporter_id ) );
			$content = ob_get_clean();
			$status  = 'update';
		} else {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			$result = wp_delete_user( $supporter_id );
			if ( ! $result ) {
				$error = esc_html__( 'Can not delete this supporter!!!', 'sts' );
			}
			$target = '#tr-' . $supporter_id;
			$status = 'delete';
		}
	} else {
		$error = esc_html__( "You have not permission to delete this supporter", 'sts' );
	}

} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array( 'target' => $target, 'content' => $content ), $status );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
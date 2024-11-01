<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_unlock_user', 'nonce' )
) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$user_id = absint( sanitize_text_field( $_POST['id'] ) );
	if ( $user_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '.form__message',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'supporter' )
	     || user_can( $current_user, 'leader_supporter' ) ) {
		$is_lock = get_user_meta( $user_id, 'sts_is_lock', true );
		if ( $is_lock == 1 ) {
			update_user_meta( $user_id, 'sts_is_lock', 0 );
			$user_meta  = get_userdata( $user_id );
			$user_roles = $user_meta->roles;
			if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) ) {
				$action = 'sts_delete_supporter';
				$nonce  = wp_create_nonce( 'sts_frontend_delete_supporter' );
			} elseif ( in_array( 'subscriber', $user_roles ) ) {
				$action = 'sts_delete_customer';
				$nonce  = wp_create_nonce( 'sts_frontend_delete_customer' );
			}
			ob_start();
			STS()->get_template( 'users/delete-user.php',
				array( 'user_id' => $user_id, 'action' => $action, 'nonce' => $nonce ) );
			$content = ob_get_clean();
		} else {
			$error = esc_html__( 'Can not unlock this user!!!', 'sts' );
		}
	} else {
		$error = esc_html__( "You have not permission to unlock this user", 'sts' );
	}

} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array( 'target' => '#sts-lock-user-' . $user_id, 'content' => $content ), 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
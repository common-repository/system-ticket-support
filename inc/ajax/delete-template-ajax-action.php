<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_delete_template', 'nonce' )
) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$id = absint( sanitize_text_field( $_POST['id'] ) );
	if ( $id === 0 ) {
		sts_send_json(
			array(
				'target'  => '.form__message',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$template = STS()->db()->templates()->getting_template_by_id( $id );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || ( user_can( $current_user, 'supporter' ) && ( $template->user_id == $current_user->ID && $template->is_public == 0 ) ) ) {
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		$result       = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_prefix}template where id=%d",
			$id ) );
		if ( $result === false || $result === 0 ) {
			$error = esc_html__( 'Can not delete this templates!!!', 'sts' );
		} else {
			$templates = STS()->db()->templates()->getting_template_by_user( $current_user->ID );
			$is_last   = count( $templates );
			$message   = '';
			if ( $is_last == 1 ) {
				$message = esc_html__( 'No template found!!', 'sts' );
			}
		}

	} else {
		$error = esc_html__( "You have not permission to delete this template", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array( 'target' => '#tr-' . $id, 'is_last' => $is_last, 'message' => $message ), 'delete' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
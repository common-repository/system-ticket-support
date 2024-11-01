<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_get_template_security', 'nonce' ) ) {
	$template_id = absint( sanitize_text_field( $_POST['id'] ) );
	if ( $template_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-reply-ticket',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$user_signature  = '';
	$is_on_signature = get_user_meta( $current_user->ID, 'sts_is_on_signature', true );
	if ( $is_on_signature == 1 ) {
		$user_signature = get_user_meta( $current_user->ID, 'sts_user_signature', true );
	}
	if ( $template_id == 0 ) {
		$content = $user_signature;
	} else {
		$template = STS()->db()->templates()->getting_template_by_id( $template_id );
		if ( ( user_can( $current_user, 'administrator' )
		       || user_can( $current_user, 'leader_supporter' )
		       || user_can( $current_user, 'supporter' ) && ( ( $template->user_id == $current_user->ID && $template->is_public == 0 ) || $template->is_public == 1 ) ) ) {

			$template_value = $template->template_value;
			$content        = $template_value . $user_signature;
		} else {
			$error = esc_html__( "You can not get this template!", 'sts' );
		}
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'content' => $content,
		'target'  => 'editor',
	), '' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-reply-ticket',
			'message' => $error
		),
		'alert' );
}

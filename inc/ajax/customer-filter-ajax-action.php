<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_filter_customer_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	$user_meta    = get_userdata( $current_user->ID );
	$user_roles   = $user_meta->roles;
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) ) {
		$limit        = STS()->db()->limit_user;
		$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
		if ( $current_page === 0 ) {
			$current_page = 1;
		}
		$offset    = $limit * ( $current_page - 1 );
		$key_word  = sanitize_text_field( $_POST['keyWord'] );
		$arr_param = apply_filters( 'sts_filter_customer',
			array(
				'keyword'      => $key_word,
				'offset'       => $offset,
				'limit'        => $limit,
				'current_page' => $current_page
			) );
	} else {
		$error = esc_html__( "You have not permission to do this action", 'sts' );
	}
} else {
	$error =
		esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( $arr_param
		, 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
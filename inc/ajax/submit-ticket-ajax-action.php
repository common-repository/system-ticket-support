<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = array();
if ( isset( $_POST['sts_submit_ticket_field'] ) &&
     wp_verify_nonce( $_POST['sts_submit_ticket_field'], 'processing-submit-ticket' )
) {
	global $wpdb;
	$table_prefix = STS()->db()->table_prefix();
	$themeId      = sanitize_text_field( $_POST['theme'] );
	$theme        = STS()->db()->themes()->getting_theme_by_id( $themeId );
	$current_user = wp_get_current_user();
	if ( $theme->status == 1 ) {
		$subject = sanitize_text_field( stripslashes( $_POST['ticketSubject'] ) );
		if ( isset( $_POST['relatedUrl'] ) ) {
			$relatedUrl = esc_url_raw( $_POST['relatedUrl'] );
			if ( $relatedUrl == '' ) {
				$error[] = array(
					'message' => esc_html__( 'Please fill in required field!', 'sts' ),
					'target'  => '.form__message'
				);
			}
		} else {
			$relatedUrl = '';
		}
		$purchase = '';
		if ( isset( $_POST['purchaseCode'] ) ) {
			$purchase = sanitize_text_field( $_POST['purchaseCode'] );
		}

		$ticketMessage = wpautop( stripslashes( wp_filter_post_kses( $_POST['ticketMessage'] ) ) );
		$files         = $_FILES['attachment'];
		$firstName     = '';
		$lastName      = '';
		$email         = '';
		$password      = '';
		$rppassword    = '';
		if ( ! $current_user->exists() ) {
			$firstName  = sanitize_text_field( $_POST['firstName'] );
			$lastName   = sanitize_text_field( $_POST['lastName'] );
			$email      = sanitize_email( $_POST['email'] );
			$password   = sanitize_text_field( $_POST['password'] );
			$rppassword = sanitize_text_field( $_POST['rppassword'] );
			$is_new     = true;
		}
		$result = apply_filters( 'sts_submit_ticket', array(
			'firstName'     => $firstName,
			'lastName'      => $lastName,
			'email'         => $email,
			'password'      => $password,
			'rpassword'     => $rppassword,
			'purchase'      => $purchase,
			'relateUrl'     => $relatedUrl,
			'subject'       => $subject,
			'ticketMessage' => $ticketMessage,
			'files'         => $files,
			'themeId'       => $themeId
		) );
		$is_new = $result['is_new'];
		$error  = $result['errors'];

	} else {
		$error_message = esc_html__( "This theme is not active!!", 'sts' );
		$target        = '.form__message';
		$error[]       = array( 'message' => $error_message, 'target' => $target );
	}
} else {
	$error_message = esc_html__( "Error", 'sts' );
	$target        = '.form__message';
	$error[]       = array( 'message' => $error_message, 'target' => $target );
}
if ( count( $error ) == 0 ) {
	if ( $is_new ) {
		sts_send_json(
			array(
				'target'  => '.form__message',
				'message' => esc_html__( 'You have register success. Please confirm your email to verify your account!!', 'sts' ),
				'url'     => sts_support_page_url( 'tickets' )
			),
			'alert-success' );
	} else {
		sts_send_json(
			sts_support_page_url( 'tickets' ), 'redirect' );
	}

} else {
	sts_send_json(
		array(
			'error_arr' => $error
		),
		'alert-multi' );
}
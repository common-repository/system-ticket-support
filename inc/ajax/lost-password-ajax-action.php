<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_lost_password_nonce_field'] )
     && wp_verify_nonce( $_POST['sts_lost_password_nonce_field'], 'processing-lost-password' )
) {
	$login_user = sanitize_text_field( $_POST['userLogin'] );
	if ( empty( $login_user ) ) {
		sts_send_json(
			array(
				'target'  => '.form__message',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$error = '';
	if ( ! is_email( $login_user ) ) {
		$user = get_user_by( 'login', $login_user );
		if ( ! $user ) {
			$error = esc_html__( 'This user has not existed!!!', 'sts' );
		} else {
			$email = $user->user_email;
		}
	} else {
		$user = get_user_by( 'email', $login_user );
		if ( ! $user ) {
			$error = esc_html__( 'This user has not existed!!!', 'sts' );
		} else {
			$email = $user->user_email;
		}
	}
	if ( $error == '' ) {
		$template = STS()->db()->mail_template()->get_mail_template_by_key( 'mail_reset_password' );
		if ( $template ) {
			$subject = $template->subject;
			$message = sts_replace_content( $template->content,
				array( '%customer_name', '%reset_password_url', '%url_unsubscribe' ),
				array(
					$user->display_name,
					'<a href="' . esc_url( sts_support_page_url( 'reset-password/?email=' . $email . '&key_reset=' . wp_create_nonce( 'sts_lost_password_security' ) ) ) . '" style="color:white;">' . esc_html__( 'Link for reser password', 'sts' ) . '</a>',
					'<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $user->ID . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
				)
			);
		} else {
			$subject = "Mail reset password";
			ob_start();
			STS()->get_template( 'mails/template-mail-reset-password.php',
				array(
					'customer_name'      => $user->display_name,
					'reset_password_url' => '<a href="' . esc_url( sts_support_page_url( 'reset-password/?email=' . $email . '&key_reset=' . wp_create_nonce( 'sts_lost_password_security' ) ) ) . '" style="color:white;">' . esc_html__( 'Link for reser password', 'sts' ) . '</a>',
					'unsubscribe_url'    => '<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $user->ID . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
				) );
			$message = ob_get_clean();
		}

		$result = sts_send_mail( $email, $subject, $message );
		if ( ! $result ) {
			global $phpmailer;
			if ( isset( $phpmailer ) ) {
				$error = $phpmailer->ErrorInfo;
			}
		}
	}
} else {
	$error = esc_html__( 'You have not permission to do this action!', 'sts' );
}
if ( $error == '' ) {
	$success_msg = esc_html__( 'Please confirm your email to create your new password!!!', 'sts' );
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $success_msg
		),
		'alert-success' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
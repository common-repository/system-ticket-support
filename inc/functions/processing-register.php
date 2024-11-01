<?php
function sts_processing_register( $firstName, $lastName, $email, $password, $rppassword ) {
	$error  = array();
	$target = '.form__message';
	if ( $firstName == '' || $lastName == '' || $email == '' || $password == '' || $rppassword == '' ) {
		$error_message = esc_html__( 'Please fill in required field!', 'sts' );
		$error[]       = array( 'message' => $error_message, 'target' => $target );
	} elseif ( ! is_email( $email ) ) {
		$error_message = esc_html__( 'Email is invalid!', 'sts' );
		$target        = '#form-control-message-email';
		$error[]       = array( 'message' => $error_message, 'target' => $target );
	} elseif ( $password != $rppassword ) {
		$error_message = esc_html__( 'Password and repeat password not same!', 'sts' );
		$target        = '#form-control-message-password';
		$error[]       = array( 'message' => $error_message, 'target' => $target );
	} else {
		$name = $firstName . ' ' . $lastName;

		if ( username_exists( $email ) || email_exists( $email ) ) {
			$error_message = esc_html__( 'The email has existed! If you are, please ', 'sts' ) .
			                 '<a href="' . esc_url( sts_support_page_url( 'login' ) ) . '">' . esc_html__( 'login', 'sts' ) . '</a>' . esc_html__( ' or ', 'sts' ) .
			                 '<a href="' . esc_url( sts_support_page_url( 'lost-password' ) ) . '">' . esc_html__( 'forgot password.', 'sts' ) . '</a>';
			$target        = '#form-control-message-email';
			$error[]       = array( 'message' => $error_message, 'target' => $target );
		} elseif ( count( $error ) == 0 ) {
			$user_id = register_new_user( $email, $email );
			if ( is_wp_error( $user_id ) ) {
				$error[] = array( 'message' => $user_id->get_error_message(), 'target' => $target );
			} else {
				$userdata = array(
					'ID'            => $user_id,
					'user_pass'     => $password,
					'user_nicename' => $name,
					'display_name'  => $name,
					'first_name'    => $firstName,
					'last_name'     => $lastName
				);
				wp_update_user( $userdata );
				wp_set_auth_cookie( $user_id );
				wp_set_current_user( $user_id );
				do_action( 'wp_login', $email, get_user_by( 'ID', $user_id ) );
				$current_user = wp_get_current_user();
				update_user_meta( $current_user->ID, 'sts_first_login', 1 );
				update_user_meta( $current_user->ID, 'sts_is_verified', 0 );
				update_user_meta( $current_user->ID, 'sts_is_receive_mail', 1 );
				$template             = STS()->db()->mail_template()->get_mail_template_by_key( 'mail_register' );
				$nonce_verify_account = wp_create_nonce( 'sts_user_verify_account_security' );
				if ( $template ) {
					$subject = $template->subject;
					$message = sts_replace_content( $template->content,
						array( '%customer_name', '%profile_url', '%url_unsubscribe' ),
						array(
							$current_user->display_name,
							'<a href="' . esc_url( sts_support_page_url( 'user-profile/?key_verify=' . $nonce_verify_account ) ) . '" style="color:white;">' . esc_html__( ' Verify your account. ', 'sts' ) . '</a>',
							'<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $current_user->ID . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
						)
					);
				} else {
					$subject = esc_html__( 'Mail confirm register', 'sts' );
					ob_start();
					STS()->get_template( 'mails/template-mail-register.php',
						array(
							'customer_name'   => $current_user->display_name,
							'profile_url'     => '<a href="' . esc_url( sts_support_page_url( 'user-profile/?key_verify=' . $nonce_verify_account ) ) . '" style="color:white;">' . esc_html__( ' Verify your account. ', 'sts' ) . '</a>',
							'unsubscribe_url' => '<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $current_user->ID . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
						) );
					$message = ob_get_clean();
				}
				$sent = sts_send_mail( $email, $subject, $message );
				if ( ! $sent ) {
					global $phpmailer;
					if ( isset( $phpmailer ) ) {
						error_log( $phpmailer->ErrorInfo );
					}

				}
			}
		}

	}

	return array( 'error' => $error );
}
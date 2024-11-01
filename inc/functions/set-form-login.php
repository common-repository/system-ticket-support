<?php
function sts_set_content_form_login() {
	$defaults          = array(
		'echo'           => true,
		// Default 'redirect' value takes the user back to the request URI.
		'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'form_id'        => 'loginform',
		'label_username' => __( 'Username or Email Address*', 'sts' ),
		'label_password' => __( 'Password*', 'sts' ),
		'label_remember' => __( 'Remember Me', 'sts' ),
		'label_log_in'   => __( 'LogIn', 'sts' ),
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'remember'       => true,
		'value_username' => '',
		// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
		'value_remember' => false,
	);
	$args              = apply_filters( 'login_form_defaults', $defaults );
	$login_form_top    = apply_filters( 'login_form_top', '', $args );
	$login_form_middle = apply_filters( 'login_form_middle', '', $args );
	$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

	return array(
		'args'              => $args,
		'login_form_top'    => $login_form_top,
		'login_form_middle' => $login_form_middle,
		'login_form_bottom' => $login_form_bottom
	);
}

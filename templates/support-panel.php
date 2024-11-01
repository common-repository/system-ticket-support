<?php
/**
 * Header Template
 */
STS()->get_template( 'header.php' );
$current_user = wp_get_current_user();
if ( isset( $_GET['key'] ) && $_GET['key'] != '' ) {
	if ( isset( $_GET['t'] ) && $_GET['t'] != '' ) {
		$date      = date( "Y-m-d H:i:s" );
		$ticket_id = sanitize_text_field( $_GET['t'] );
		STS()->db()->tickets()->update_link_rating_visited( $date, $ticket_id );
	}
}
$current_endpoint = '';
if ( has_filter( 'ena_get_current_endpoint' ) ) {
	$current_endpoint = apply_filters( 'ena_get_current_endpoint', array() );
}
if ( $current_endpoint == '' ) {
	$current_endpoint = STS()->endpoints()->get_current_endpoint();
}
$is_lock = get_user_meta( $current_user->ID, 'sts_is_lock', true );
if ( is_user_logged_in() && $is_lock == 0 ) {
	/**
	 * Do action endpoint template
	 */
	do_action( 'tst_endpoint_template_' . $current_endpoint );
	if ( ! has_action( 'tst_endpoint_template_' . $current_endpoint ) ) {
		STS()->get_template( 'endpoints/dashboards.php' );
	}
} else {
	do_action( 'tst_endpoint_template_no_login_' . $current_endpoint );
	if ( ! has_action( 'tst_endpoint_template_no_login_' . $current_endpoint ) ) {

		STS()->get_template( 'endpoints/login.php',
			sts_set_content_form_login() );
	}
}

/**
 * Footer Template
 */
STS()->get_template( 'footer.php' );
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_listing_ticket_filter_nonce_field'] ) &&
     wp_verify_nonce( $_POST['sts_listing_ticket_filter_nonce_field'], 'processing-listing-ticket' ) ) {
	global $wpdb;
	$keyword      = sanitize_text_field( $_POST['keyWord'] );
	$theme        = sanitize_text_field( $_POST['theme'] );
	$ftstatus     = absint( sanitize_text_field( $_POST['ftstatus'] ) );
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'subscriber' ) || user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'leader_supporter' ) ) {
		$user_meta    = get_userdata( $current_user->ID );
		$user_roles   = $user_meta->roles;
		$status       = absint( sanitize_text_field( $_POST['current_status'] ) );
		$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
		if ( $current_page === 0 ) {
			$current_page = 1;
		}
		$limit        = STS()->db()->tickets()->limit_ticket;
		$offset       = $limit * ( $current_page - 1 );
		$table_prefix = STS()->db()->table_prefix();
		$ticket_arr   = apply_filters( 'sts_get_filter_ticket', array(
			'status'   => $status,
			'keyword'  => $keyword,
			'ftstatus' => $ftstatus,
			'user_id'  => $current_user->ID,
			'theme'    => $theme,
			'offset'   => $offset
		) );

		$tickets    = $ticket_arr['tickets'];
		$allTickets = $ticket_arr['all_tickets'];
		if ( $tickets ) {
			$contents = sts_set_ticket( $tickets );
			ob_start();
			STS()->get_template( 'tickets/ticket-item.php', array( 'contents' => $contents ) );
			if ( $allTickets > $limit ) {
				$numbPage = ceil( $allTickets / $limit );
				STS()->get_template( 'paginator.php', array(
					'numbPage'     => $numbPage,
					'current_page' => $current_page,
					'target'       => '#form-listing-ticket',
					'limit'        => $limit,
					'total'        => $allTickets
				) );
			}
			$success_message = ob_get_clean();
			$arr_param       = array(
				'target'  => '.listing-ticket__initing',
				'content' => $success_message,
			);

		} else {
			$arr_param = array(
				'target'  => '.listing-ticket__initing',
				'content' => '<div class="sts-no-content">' . esc_html__( 'No ticket found!!', 'sts' ) . '</div>',
			);
		}
	} else {
		$error = esc_html__( "You have not permission to do this action", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( $arr_param, 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
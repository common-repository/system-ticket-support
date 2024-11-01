<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_customer_paginator_ticket_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	$customer_id  = absint( sanitize_text_field( $_POST['customer_id'] ) );
	$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
	if ( $customer_id === 0 || $current_page === 0 ) {
		$error = esc_html__( 'Error', 'sts' );
		sts_send_json(
			array(
				'target'  => '.form__message',
				'message' => $error
			),
			'alert' );
	}
	sts_logout_locked( $current_user->ID );
	if ( $current_user->ID == $customer_id || user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) ) {

		$customer = get_user_by( 'ID', $customer_id );
		if ( $customer === false ) {
			$error = esc_html__( 'Error', 'sts' );
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => $error
				),
				'alert' );
		}
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		$limit        = STS()->db()->tickets()->limit_ticket;
		$offset       = $limit * ( $current_page - 1 );
		$tickets      = STS()->db()->tickets()->get_ticket_by_customer_limit( $customer_id, $offset );
		$all_ticket   = count( STS()->db()->tickets()->get_ticket_by_customer_all( $customer_id ) );
		$numbPage     = ceil( $all_ticket / $limit );
		if ( $tickets ) {
			$contents = sts_set_ticket( $tickets );
			ob_start();
			STS()->get_template( 'tickets/ticket-item.php', array( 'contents' => $contents ) );
			$content = ob_get_clean();
			ob_start();
			STS()->get_template( 'paginator.php',
				array(
					'numbPage'     => $numbPage,
					'current_page' => $current_page,
					'target'       => '#form-paginator-customer-ticket',
					'limit'        => $limit,
					'total'        => $all_ticket
				) );
			$paginator = ob_get_clean();
			$arr_param = array(
				'target'            => '.customer-ticket__items',
				'content'           => $content,
				'current_page'      => intval( $current_page ),
				'total_page'        => $numbPage,
				'target_paginator'  => '.customer-ticket-paginator',
				'content_paginator' => $paginator
			);

		} else {
			$arr_param = array(
				'target'  => '.customer-ticket__items',
				'content' => '<div class="sts-no-content">' . esc_html__( 'No ticket found!!', 'sts' ) . '</div>'
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
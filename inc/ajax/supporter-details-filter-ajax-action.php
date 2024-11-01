<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_filter_ticket_nonce_field', 'nonce' ) ) {
	global $wpdb;
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
		$key_word       = sanitize_text_field( ( $_POST['keyWord'] ) );
		$status         = sanitize_text_field( $_POST['ftstatus'] );
		$from_date      = sanitize_text_field( $_POST['fromDate'] );
		$to_date        = sanitize_text_field( $_POST['toDate'] );
		$current_page   = absint( sanitize_text_field( $_POST['current_page'] ) );
		$current_status = absint( sanitize_text_field( $_POST['current_status'] ) );
		if (  $current_page === 0 ) {
			$current_page = 1;
		}
		$table_prefix = STS()->db()->table_prefix();
		$date         = date( "Y-m-d H:i:s" );
		if ( $from_date != '' ) {
			$from_date = date_format( date_create( $from_date ), "Y-m-d H:i:s" );
		} else {
			$from_date = '1900-01-01 00:00:00';
		}
		if ( $to_date != '' ) {
			$to_date = date_format( date_create( $to_date ), "Y-m-d H:i:s" );
		} else {
			$to_date = $date;
		}
		$supporter_id = absint( sanitize_text_field( $_POST['supporterID'] ) );
		$search       = $wpdb->esc_like( $key_word );
		$limit        = STS()->db()->tickets()->limit_ticket;
		$search       = '%' . $key_word . '%';
		$offset       = $limit * ( $current_page - 1 );
		$arr          = STS()->db()->tickets()->filter_ticket_of_supporter( $current_status, $status, $search, $from_date, $to_date, $supporter_id, $limit, $offset );
		$tickets      = $arr['tickets'];
		$ticketNum    = $arr['numb_tickets'];

		if ( $tickets ) {
			$contents = sts_set_ticket( $tickets );
			ob_start();
			STS()->get_template( 'tickets/ticket-item.php', array( 'contents' => $contents ) );
			if ( $ticketNum > $limit ) {
				$numbPage = ceil( $ticketNum / $limit );
				STS()->get_template( 'paginator.php', array(
					'numbPage'     => $numbPage,
					'current_page' => $current_page,
					'target'       => '#supporter-ticket-filter',
					'limit'        => $limit,
					'total'        => $ticketNum
				) );
			}
			$success_message = ob_get_clean();
			$arr_param       = array(
				'target'  => '.supporter-ticket__init',
				'content' => $success_message,
			);
		} else {
			$arr_param = array(
				'target'  => '.supporter-ticket__init',
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
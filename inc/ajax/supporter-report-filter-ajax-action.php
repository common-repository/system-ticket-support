<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_supporter_report_filter_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
		$date      = date( "Y-m-d H:i:s" );
		$from_date = sanitize_text_field( $_POST['fromDate'] );
		$to_date   = sanitize_text_field( $_POST['toDate'] );
		if ( $from_date == '' ) {
			$from_date = date( "Y-m-d" );
		}
		if ( $to_date == '' ) {
			$to_date = date( "Y-m-d" );
		}
		$supporters = get_users( array( 'role__in' => array( 'supporter', 'leader_supporter', 'administrator' ) ) );
		if ( $supporters ) {
			$contents = array();
			foreach ( $supporters as $supporter ) {
				$nb_message = STS()->db()->messages()->report_message_by_supporter( $supporter->ID, $from_date, $to_date );
				$nb_ticket  = STS()->db()->tickets()->supporter_report( $supporter->ID, $from_date, $to_date );
				$contents[] = array(
					'supporter_name' => $supporter->display_name,
					'from_date'      => $from_date,
					'to_date'        => $to_date,
					'nb_message'     => $nb_message,
					'nb_ticket'      => $nb_ticket,
					'supporter_id'   => $supporter->ID,
				);
			}
			ob_start();
			STS()->get_template( 'reports/supporter-report-item.php',
				array(
					'contents'     => $contents,
					'current_page' => 1,
				) );
			$content = ob_get_clean();
		} else {
			$content = '<td colspan="100%">' . esc_html__( 'No data!!', 'sts' ) . '</td>';
		}

	} else {
		$error = esc_html__( "You have not permission to view report!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		array(
			'target'  => 'table.supporter-report-table>tbody',
			'content' => $content,
		),
		'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
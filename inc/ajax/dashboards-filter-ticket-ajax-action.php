<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_dashboards_get_ticket_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) ) {
		$user = sanitize_text_field( $_POST['user'] );
		if ( empty( $user ) ) {
			$user = 'all';
		}
		if ( $user == 'all' ) {
			$content_filter = esc_html__( 'All', 'sts' );
		} elseif ( $user == 'my-ticket' ) {
			$content_filter = esc_html__( 'My ticket', 'sts' );
		}
		$tickets = STS()->db()->tickets()->get_older_ticket( $user, $current_user->ID );
		if ( $tickets ) {
			$contents = array();
			foreach ( $tickets as $ticket ) {
				$contents[] = array(
					'url'           => sts_link_ticket( $ticket->id ),
					'subject'       => $ticket->subject,
					'updating_date' => sts_time_ago( strtotime( sts_set_updating_date( $ticket->id ) ) )
				);
			}
			ob_start();
			STS()->get_template( 'dashboards/ticket-item.php',
				array( 'tickets' => $contents ) );
			$content = ob_get_clean();
		} else {
			$content = '<div class="sts-no-content">' . esc_html__( 'No ticket found!!', 'sts' ) . '</div>';
		}
	} else {
		$error = esc_html__( "You have not permission to do this action", 'sts' );
	}
} else {
	$error =
		esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
			'target'          => '.dashboards-ticket',
			'content'         => $content,
			'target_filter'   => '.dashboards-distribution-header-current',
			'content_filter'  => $content_filter,
			'dropdown_toggle' => '.dashboards-distribution-header-toggle'
		)
		, 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}

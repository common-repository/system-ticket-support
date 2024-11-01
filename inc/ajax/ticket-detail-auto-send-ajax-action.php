<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_ticket_details_auto_send_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'leader_supporter' ) ) {
		$ticket_id = absint( sanitize_text_field( $_POST['ticket_id'] ) );
		if ( $ticket_id === 0 ) {
			return;
		}
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		$ticket       = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
		if ( is_null( $ticket ) ) {
			return;
		}
		$is_lock = $ticket->is_lock;
		if ( isset( $is_lock ) && $is_lock == 1 ) {
			$user = get_user_by( 'ID', $ticket->user_lock );
			if ( $ticket->user_lock != $current_user->ID ) {
				ob_start();
				STS()->get_template( 'tickets/supporter-processing.php', array( 'supporter_name' => $user->display_name ) );
				$content = ob_get_clean();
				$target  = '.supporter-processing-anchor';
				ob_start();
				STS()->get_template( 'tickets/supporter-marking-process.php',
					array(
						'supporter_name' => $user->display_name,
					) );
				$content_unmark = ob_get_clean();
				$target_unmark  = '.single-ticket__meta-process';
				if ( user_can( $current_user, 'administrator' ) ) {
					$close_content = '';
				} else {
					$close_content = '.single-ticket__action';
				}

			} else {
				$nonce = wp_create_nonce( 'sts_unmark_process_security' );
				ob_start();
				STS()->get_template( 'ticket-details/unmark-process.php', array(
					'ticket_id' => $ticket_id,
					'nonce'     => $nonce
				) );
				$content_unmark = ob_get_clean();
				$target_unmark  = '.single-ticket__meta-process';
				ob_start();
				STS()->get_template( 'tickets/supporter-processing.php', array( 'supporter_name' => $user->display_name ) );
				$content       = ob_get_clean();
				$target        = '.supporter-processing-anchor';
				$close_content = '';

			}
			$contents = array(
				'target'              => $target,
				'content'             => $content,
				'close_content'       => $close_content,
				'open_content_anchor' => '.supporter-processing-anchor',
				'content_unmark'      => $content_unmark,
				'target_unmark'       => $target_unmark,
				'open_content'        => '.single-ticket__meta-process',
			);
		} else {
			ob_start();
			STS()->get_template( 'ticket-details/mark-process.php', array(
				'ticket_id' => $ticket_id,
				'nonce'     => wp_create_nonce( 'sts_mark_process_security' )
			) );
			$content  = ob_get_clean();
			$contents = array(
				'target'        => '.single-ticket__meta-process',
				'content'       => $content,
				'open_content'  => '.single-ticket__action,.single-ticket__meta-process',
				'close_content' => '.supporter-processing-anchor'
			);
		}
		sts_send_json(
			$contents,
			'' );
	}
}
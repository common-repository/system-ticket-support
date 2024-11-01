<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_auto_sending_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'leader_supporter' ) ) {
		$ticket_ids = stripslashes( sanitize_text_field( $_POST['ticket_ids'] ) );
		$ticket_ids = explode( ",", $ticket_ids );
		global $wpdb;
		$table_prefix = STS()->db()->table_prefix();
		$ids          = array();
		foreach ( $ticket_ids as $ticket_id ) {
			$ticket_update = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM {$table_prefix}ticket WHERE id=%d",
				$ticket_id
			) );
			if ( ! is_null( $ticket_update->lock_date ) && $ticket_update->is_lock == 1
			) {
				$supporter = get_user_by( 'ID', $ticket_update->user_lock );
				ob_start();
				STS()->get_template( 'tickets/supporter-marking-process.php', array( 'supporter_name' => $supporter->display_name ) );
				$supporter_process = ob_get_clean();
				$ids[]             = array(
					'id'        => $ticket_id,
					'target'    => '#ticket__meta-supporter-' . $ticket_id,
					'supporter' => $supporter_process
				);
			} elseif ( is_null( $ticket_update->lock_date ) && $ticket_update->is_lock == 0 ) {
				$ids[] = array(
					'id'        => $ticket_id,
					'target'    => '#ticket__meta-supporter-' . $ticket_id,
					'supporter' => ''
				);
			}

		}
		if ( count( $ids ) > 0 ) {
			$content = $ids;
		} else {
			$content = '';
		}
		sts_send_json(
			$content,
			'' );
	}
}

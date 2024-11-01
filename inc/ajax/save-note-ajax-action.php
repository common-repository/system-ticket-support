<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_new_note_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$ticket_id    = absint( sanitize_text_field( $_POST['ticket_id'] ) );
	$supporter_id = absint( sanitize_text_field( $_POST['supporter_id'] ) );
	if ( $ticket_id === 0 || $supporter_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-note',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	if ( is_null( $ticket ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-note',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) ) {
		global $wpdb;
		$note_content = sanitize_text_field( $_POST['note_content'] );
		$date         = date( "Y-m-d H:i:s" );
		$table_prefix = STS()->db()->table_prefix();
		$table_name   = $table_prefix . 'note';
		$result       = $wpdb->insert(
			$table_name,
			array(
				'message'      => $note_content,
				'created_date' => $date,
				'ticket_id'    => $ticket_id,
				'user_id'      => $supporter_id
			)
		);
		$note_id      = $wpdb->insert_id;
		if ( $result !== false && $result !== 0 ) {
			$supporter  = get_user_by( 'ID', $supporter_id );
			$note_items = array(
				'message'      => $note_content,
				'created_date' => $date,
				'ticket_id'    => $ticket_id,
				'user_id'      => $supporter_id,
				'note_id'      => $note_id,
				'name'         => $supporter->display_name,
			);
			ob_start();
			STS()->get_template( 'ticket-details/note-item.php', array(
				'note'  => $note_items,
				'nonce' => wp_create_nonce( 'sts_delete_note_security' )
			) );
			$contents = ob_get_clean();

		} else {
			$error = esc_html__( 'Can not save note', 'sts' );
		}
	} else {
		$error = esc_html__( "You have not permission to add new note", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'target'        => '.note__items',
		'content'       => $contents,
		'close_content' => '#note__new-form'
	), 'append' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-note',
			'message' => $error
		),
		'alert' );
}
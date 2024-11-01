<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_delete_note_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$note_id = absint( sanitize_text_field( $_POST['id'] ) );
	if ( $note_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-note',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$table_prefix = STS()->db()->table_prefix();
	global $wpdb;
	$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$table_prefix}note WHERE id=%d", $note_id ) );
	if ( user_can( $current_user, 'administrator' ) || $current_user->ID == $user_id ) {
		$table_name = $table_prefix . 'note';
		$result     = $wpdb->query( $wpdb->prepare( "Delete from {$table_name} WHERE id=%d", $note_id ) );
		if ( $result === false || $result === 0 ) {
			$error = esc_html__( "Can not delete this note!", 'sts' );
		}
	} else {
		$error =
			esc_html__( "You have not permission to delete this note!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array( 'target' => '#note-' . $note_id, 'is_last' => 0, 'message' => '' ), 'delete' );
} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-note',
			'message' => $error
		),
		'alert' );
}
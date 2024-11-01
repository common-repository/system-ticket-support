<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_delete_category', 'nonce' )
) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
		global $wpdb;
		$id = absint( sanitize_text_field( $_POST['id'] ) );
		if ( $id === 0 ) {
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		$themes = STS()->db()->themes()->getting_theme_all();
		$theme  = STS()->db()->themes()->getting_theme_by_theme_id( $id );
		if ( is_null( $theme ) ) {
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		$nb_ticket = STS()->db()->tickets()->count_ticket_by_theme( $theme->theme_id );
		if ( intval( $nb_ticket ) == 0 ) {
			$table_prefix = STS()->db()->table_prefix();
			$result       = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_prefix}theme where id=%d",
				$id ) );
			if ( $result === false || $result === 0 ) {
				$error = esc_html__( 'Can not delete the category!!!', 'sts' );

			} else {
				$is_last = count( $themes );
				$message = '';
				if ( $is_last == 1 ) {
					$message = esc_html__( 'No category found!!', 'sts' );
				}
			}
		} else {
			$error = esc_html__( 'Can not delete the category!!!', 'sts' );
		}

	} else {
		$error =
			esc_html__( "You have not permission to delete this category!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array( 'target' => '#tr-' . $id, 'is_last' => $is_last, 'message' => $message ), 'delete' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
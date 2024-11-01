<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_unlock_template', 'nonce' )
) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' ) ) {
		global $wpdb;
		$id = absint( sanitize_text_field( $_POST['id'] ) );
		if (  $id === 0 ) {
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		$template = STS()->db()->templates()->getting_template_by_id( $id );
		if ( is_null( $template ) ) {
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		if ( $template->is_public == 0 ) {
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'template';
			$result       = $wpdb->update(
				$table_name,
				array( 'is_public' => 1 ),
				array( 'id' => $id )
			);
			if ( $result === false ) {
				$error = esc_html__( 'Can not set public for this template!!!', 'sts' );

			} else {
				ob_start();
				STS()->get_template( 'template/lock-template.php', array(
					'template_id' => $id,
					'nonce'       => wp_create_nonce( 'sts_frontend_lock_template' )
				) );
				$content = ob_get_clean();
			}
		}
	} else {
		$error =
			esc_html__( "You have not permission to set public for this template!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		array(
			'target'         => '#sts-lock-template-' . $id,
			'content'        => $content,
			'target_status'  => '.sts-template-type-' . $id,
			'content_status' => esc_html__( 'Public', 'sts' )
		), 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
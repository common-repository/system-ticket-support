<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_frontend_unlock_category', 'nonce' )
) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
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
		$theme = STS()->db()->themes()->getting_theme_by_theme_id( $id );
		if ( is_null( $theme ) ) {
			sts_send_json(
				array(
					'target'  => '.form__message',
					'message' => esc_html__( 'Error', 'sts' )
				),
				'alert' );
		}
		if ( $theme->status == 0 ) {
			$table_prefix = STS()->db()->table_prefix();
			$table_name   = $table_prefix . 'theme';
			$result       = $wpdb->update(
				$table_name,
				array( 'status' => 1 ),
				array( 'id' => $id )
			);
			if ( $result === false || $result === 0 ) {
				$error = esc_html__( 'Can not unlock the category!!!', 'sts' );

			} else {
				ob_start();
				STS()->get_template( 'category/lock-category.php', array( 'theme_id' => $id ) );
				$content = ob_get_clean();
				ob_start();
				STS()->get_template( 'category/status-valid.php', array() );
				$status = ob_get_clean();
			}
		}
	} else {
		$error =
			esc_html__( "You have not permission to unlock this category!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		array(
			'target'         => '.sts-unlock-category-' . $id,
			'content'        => $content,
			'target_status'  => '.sts-category-status-' . $id,
			'content_status' => $status
		), 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
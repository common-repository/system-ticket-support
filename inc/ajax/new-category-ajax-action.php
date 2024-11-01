<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_new_category_nonce_field'] ) && wp_verify_nonce( $_POST['sts_new_category_nonce_field'], 'processing-new-category' ) ) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
		global $wpdb;
		$theme_name = sanitize_text_field( $_POST['themeName'] );
		$theme_id   = sanitize_text_field( $_POST['themeId'] );
		if ( $theme_id == '' || $theme_name == '' ) {
			$error = esc_html__( 'Please fill in required field!!!!', 'sts' );
		} else {
			$theme        = STS()->db()->themes()->getting_theme_by_id( $theme_id );
			$table_prefix = STS()->db()->table_prefix();
			$error        = '';
			if ( $theme ) {
				$error = esc_html__( 'This id has already existed!!!', 'sts' );

			} else {
				$table_name = $table_prefix . 'theme';
				$result     = $wpdb->insert(
					$table_name,
					array(
						'theme_name' => $theme_name,
						'theme_id'   => $theme_id,
						'status'     => 1
					)
				);
				if ( $result === false || $result === 0 ) {
					$error = esc_html__( 'Can not save the category!!!', 'sts' );
				}
			}
		}

	} else {
		$error = esc_html__( "You have not permission to add new category!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		sts_support_page_url( 'categories' ),
		'redirect' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
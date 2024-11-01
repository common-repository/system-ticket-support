<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_new_temple_nonce_field'] ) && wp_verify_nonce( $_POST['sts_new_temple_nonce_field'], 'processing-new-template' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) ) {
		global $wpdb;
		$table_prefix   = STS()->db()->table_prefix();
		$template_name  = sanitize_text_field( $_POST['templateName'] );
		$template_value = wpautop( wp_filter_post_kses( $_POST['templateValue'] ) );
		$error          = '';
		if ( $template_name == '' || $template_value == '' ) {
			$error = esc_html__( 'Please fill in all field!!!!', 'sts' );
		} else {
			if ( user_can( $current_user, 'supporter' ) ) {
				$is_public = 0;
			} else {
				if ( isset( $_POST['is_public'] ) && $_POST['is_public'] == 'on' ) {
					$is_public = 1;
				} else {
					$is_public = 0;
				}
			}
			$table_name = $table_prefix . 'template';
			$result     = $wpdb->insert(
				$table_name,
				array(
					'template_name'  => $template_name,
					'template_value' => $template_value,
					'user_id'        => $current_user->ID,
					'is_public'      => $is_public
				)
			);
			if ( $result === false || $result === 0 ) {
				$error = esc_html__( 'Can not update the template!!!', 'sts' );
			} else {
				$template_id = $wpdb->insert_id;
				if ( isset( $_POST['templateTags'] ) ) {
					$template_tags =array_map('sanitize_text_field', $_POST['templateTags']);
					$table_tags    = $table_prefix . 'template_tags';
					for ( $i = 0; $i < count( $template_tags ); $i ++ ) {
						$wpdb->insert(
							$table_tags,
							array(
								'template_id' => $template_id,
								'tag'         => $template_tags[ $i ],
							)
						);
					}
				}

			}
		}

	} else {
		$error = esc_html__( "You have not permission to add new template!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		sts_support_page_url( 'templates' ),
		'redirect' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
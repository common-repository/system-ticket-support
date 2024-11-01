<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_update_temple_nonce_field'] ) &&
     wp_verify_nonce( $_POST['sts_update_temple_nonce_field'], 'processing-update-template' )
) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$id = absint( sanitize_text_field( $_POST['templateID'] ) );
	if ( $id === 0 ) {
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
	$tags = STS()->db()->templates()->get_template_tags_by_template_id( $id );
	if ( user_can( $current_user, 'administrator' )
	     || ( ( user_can( $current_user, 'leader_supporter' )
	            || user_can( $current_user, 'supporter' ) ) && $template->user_id == $current_user->ID ) ) {
		global $wpdb;
		$table_prefix   = STS()->db()->table_prefix();
		$template_name  = sanitize_text_field( $_POST['templateName'] );
		$template_value = wpautop( stripslashes( wp_filter_post_kses( $_POST['templateValue'] ) ) );
		$error          = '';
		$table_tags     = $table_prefix . 'template_tags';
		if ( $template_name == '' || $template_value == '' ) {
			$error = esc_html__( 'Please fill in required field!!!!', 'sts' );
		} else {
			if ( isset( $_POST['is_public'] ) && $_POST['is_public'] == 'on' ) {
				$is_public = 1;
			} else {
				$is_public = 0;
			}
			$table_name = $table_prefix . 'template';
			$result     = $wpdb->update(
				$table_name,
				array(
					'template_name'  => $template_name,
					'template_value' => $template_value,
					'is_public'      => $is_public
				),
				array( 'id' => $id )
			);
			if ( $result === false ) {
				$error = esc_html__( 'Can not update the template!!!', 'sts' );

			} else {
				if ( isset( $_POST['templateTags'] ) ) {
					$template_tags = $_POST['templateTags'];
					for ( $i = 0; $i < count( $template_tags ); $i ++ ) {
						$tag = STS()->db()->templates()->get_tag_by_tag( $template_tags[ $i ] );
						if ( $tag == '' ) {
							$wpdb->insert(
								$table_tags,
								array(
									'template_id' => $id,
									'tag'         => $template_tags[ $i ],
								)
							);
						}

					}
					foreach ( $tags as $tag ) {
						if ( ! in_array( $tag->tag, $template_tags ) ) {
							$wpdb->delete(
								$table_tags,
								array(
									'id' => $tag->id,
								)
							);
						}
					}
				} else {
					if ( $tags ) {
						foreach ( $tags as $tag ) {
							$wpdb->delete(
								$table_tags,
								array(
									'id' => $tag->id,
								)
							);
						}
					}
				}
			}
		}
	} else {
		$error = esc_html__( "You have not permission to update template", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => esc_html__( 'Update template success!', 'sts' )
		),
		'alert-success' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
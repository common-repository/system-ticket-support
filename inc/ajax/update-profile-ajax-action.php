<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_update_profile_nonce_field'] )
     && wp_verify_nonce( $_POST['sts_update_profile_nonce_field'], 'processing-update-profile' )
) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	$customer_id = absint( sanitize_key( $_POST['id'] ) );
	$customer    = get_user_by( 'ID', $customer_id );
	if ( ! $customer ) {
		$error = esc_html__( 'User has not existed!!', 'sts' );
	} else {
		$user_meta  = get_userdata( $customer_id );
		$user_roles = $user_meta->roles;
		if ( $current_user->ID == $customer_id
		     || user_can( $current_user, 'administrator' )
		     || ( user_can( $current_user, 'supporter' ) && in_array( 'subscriber', $user_roles ) )
		     || ( user_can( $current_user, 'leader_supporter' ) && in_array( 'subscriber', $user_roles ) ) ) {
			global $wpdb;
			$table_prefix = STS()->db()->table_prefix();
			$firstName    = sanitize_text_field( $_POST['firstName'] );
			$lastName     = sanitize_text_field( $_POST['lastName'] );
			$password     = sanitize_text_field( $_POST['password'] );
			$rppassword   = sanitize_text_field( $_POST['rppassword'] );
			$date         = date( "Y-m-d H:i:s" );
			if ( $firstName == '' || $lastName == '' ) {
				$error = esc_html__( 'Please fill in require field!!', 'sts' );
			} elseif ( $password !== $rppassword ) {
				$error = esc_html__( 'Password and repeat password not same!!', 'sts' );
			} else {
				$name      = $firstName . ' ' . $lastName;
				$files     = $_FILES['avatar'];
				$userdata  = array(
					'ID'            => $customer_id,
					'user_pass'     => $password,
					'user_nicename' => $name,
					'display_name'  => $name,
					'first_name'    => $firstName,
					'last_name'     => $lastName
				);
				$error     = '';
				$file_name = sanitize_file_name( $files['name'] );
				if ( $file_name != "" ) {
					$valid_extensions = array( "jpeg", "jpg", "png" );
					$temporary        = explode( ".", $file_name );
					$file_extension   = strtolower( end( $temporary ) );
					$file_type        = strtolower( $files['type'] );
					if ( ( ( $file_type == "image/png" ) || ( $file_type == "image/jpg" ) || ( $file_type == "image/jpeg" ) ) && in_array( $file_extension, $valid_extensions ) ) {
						if ( ! function_exists( 'wp_handle_upload' ) ) {
							require_once( ABSPATH . 'wp-admin/includes/file.php' );
						}
						$upload_overrides = array(
							'test_form' => false
						);
						$new_file_name    = sts_get_guid() . '.' . $file_extension;
						$file             = array(
							'name'     => '' . $new_file_name,
							'type'     => $file_type,
							'tmp_name' => $files['tmp_name'],
							'error'    => $files['error'],
							'size'     => $files['size']
						);
						$movefile         = wp_handle_upload( $file, $upload_overrides );
						if ( isset( $movefile['error'] ) ) {
							$error = esc_html__( $movefile['error'], 'sts' );
						} else {
							$upload_dir     = wp_get_upload_dir();
							$attachment_url = $upload_dir['subdir'] . '/' . $new_file_name;
							if ( $movefile['url'] != null ) {
								$attachment_info = pathinfo( $movefile['url'] );
								$attachment_url  = $upload_dir['subdir'] . '/' . $attachment_info['basename'];
							}
							$attachment = STS()->db()->attachments()->get_user_attachment_by_user_id( $customer_id );
							$table_name = $table_prefix . 'user_attachment';
							if ( null === $attachment ) {
								$wpdb->insert(
									$table_name,
									array(
										'user_id'        => $customer_id,
										'attachment_url' => $attachment_url
									)
								);
							} else {
								$wpdb->update(
									$table_name,
									array(
										'attachment_url' => $attachment_url
									),
									array( 'user_id' => $customer_id )
								);
							}
						}
					} else {
						$error = esc_html__( 'Please select a valid image file (JPEG/JPG/PNG)', 'sts' );
					}
				}

				if ( $error == '' ) {
					$user_id = wp_update_user( $userdata );
					if ( is_wp_error( $user_id ) ) {
						$error = esc_html__( 'Can not update profile!!', 'sts' );
					} else {
						if ( isset( $_POST['unsubscribe'] ) ) {
							if ( $_POST['unsubscribe'] == 'on' ) {
								update_user_meta( $user_id, 'sts_is_receive_mail', 0 );
							}
						} else {
							update_user_meta( $user_id, 'sts_is_receive_mail', 1 );
						}
						if ( isset( $_POST['userSignature'] ) ) {
							$user_signature = $_POST['userSignature'];
							update_user_meta( $user_id, 'sts_user_signature', wpautop( $user_signature ) );
						}
						if ( isset( $_POST['onSignature'] ) && $_POST['onSignature'] == 'on' ) {
							update_user_meta( $user_id, 'sts_is_on_signature', 1 );
						}
						if ( isset( $_POST['offSignature'] ) && $_POST['offSignature'] == 'on' ) {
							update_user_meta( $user_id, 'sts_is_on_signature', 0 );
						}
					}
				}
			}

		} else {
			$error = esc_html__( "You have not permission to update profile", 'sts' );
		}
	}

} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	if ( isset( $_POST['page'] ) ) {
		$page = $_POST['page'];
		if ( $page == 'my-profile' ) {
			$url = sts_support_page_url();
		} elseif ( $page == 'customer-details' ) {
			$url = sts_support_page_url( 'customer-details/?customer_id=' . $customer_id );
		} else {
			$url = sts_support_page_url( '' . $page );
		}
	} else {
		$url = sts_support_page_url( '' );
	}
	sts_send_json(
		$url
		, 'redirect' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
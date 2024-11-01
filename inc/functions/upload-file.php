<?php
function sts_upload_file( $files ) {
	$error      = '';
	$file_arr   = array();
	$upload_dir = wp_get_upload_dir();
	if ( $files['name'][0] != "" ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$upload_overrides = array(
			'test_form' => false
		);
		foreach ( $files['name'] as $key => $value ) {
			if ( $files['name'][ $key ] != "" ) {
				$file_name        = sanitize_file_name( $files['name'][ $key ] );
				$valid_extensions = array( "jpeg", "jpg", "png", 'gif' );
				$temporary        = explode( ".", $file_name );
				$file_extension   = strtolower( end( $temporary ) );
				$file_type        = strtolower( $files['type'][ $key ] );
				$new_file_name  = sts_get_guid() . '.' . $file_extension;
				if ( ( ( $file_type == "image/png" ) || ( $file_type == "image/jpg" )
				       || ( $file_type == "image/jpeg" ) || ( $file_type == "image/gif" ) ) && in_array( $file_extension, $valid_extensions ) ) {
					$file = array(
						'name'     => '' . $new_file_name,
						'type'     => $file_type,
						'tmp_name' => $files['tmp_name'][ $key ],
						'error'    => $files['error'][ $key ],
						'size'     => $files['size'][ $key ]
					);

					$movefile = wp_handle_upload( $file, $upload_overrides );
					if ( isset( $movefile['error'] ) ) {
						$error = esc_html__( "Sorry you can not upload your file ", "sts" );
					} else {
						$attachment_url=$upload_dir['subdir'] . '/' . $new_file_name;
						if($movefile['url']!=null){
							$attachment_info    = pathinfo( $movefile['url'] );
							$attachment_url = $upload_dir['subdir'] . '/' . $attachment_info['basename'];
						}

						$file_arr[]     = array(
							'name' => $file_name,
							'url'  => $attachment_url
						);
					}
				} else {
					$error = esc_html__( 'Please select a valid image file (JPEG/JPG/PNG/GIF)', 'sts' );
				}
			}

		}

	}

	return array( 'error' => $error, 'file_arr' => $file_arr );
}
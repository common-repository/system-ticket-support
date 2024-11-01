<?php
/**
 * Helper function for plugin
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get Support Page ID
 *
 * @return mixed|void
 */
function sts_support_page_id() {
	return intval( get_option( 'sts_support_page_id' ) );
}

/**
 * Set support page id
 *
 * @param $page_id
 */
function sts_set_support_page_id( $page_id ) {
	update_option( 'sts_support_page_id', $page_id );
}


/**
 * @param $cron_time
 */
function sts_set_cron_time( $cron_time ) {
	update_option( 'sts_cron_time', $cron_time );
}

/**
 * @param $cron_time
 */
function sts_set_cron_time_close_ticket( $cron_time ) {
	update_option( 'sts_cron_time_close_ticket', $cron_time );
}

function sts_set_menu_footer( $menus ) {
	update_option( 'sts_menu_footer', $menus );
}

function sts_set_policy_content( $content ) {
	update_option( 'sts_policy_register', $content );
}


/**
 * Check current is support page
 *
 * @return bool
 */
function sts_is_support_page() {
	if ( sts_support_page_id() == 0 ) {
		return false;
	}
	if ( is_home() ) {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front   = intval( get_option( 'page_on_front', '-1' ) );
			$support_page_id = sts_support_page_id();
			if ( $page_on_front === $support_page_id ) {
				return true;
			}
		}
	}

	return is_page( sts_support_page_id() );
}

/**
 * Get All pages
 *
 * @return array
 */
function sts_get_all_pages() {
	$pages = get_pages();
	$out   = array();
	foreach ( $pages as $page ) {
		$out[] = array(
			'id'    => $page->ID,
			'title' => $page->post_title,
		);
	}

	return $out;
}

/**
 * Get dropdown list for all pages
 *
 * @param $name
 * @param $current_page
 *
 * @return string
 */
function sts_dropdown_pages( $name, $current_page ) {
	$pages = sts_get_all_pages();
	ob_start();
	?>
    <select name="<?php echo esc_attr( $name ) ?>" id="<?php echo esc_attr( $name ) ?>">
        <option value=""><?php esc_html_e( '— Select —', 'sts' ) ?></option>
		<?php foreach ( $pages as $page ): ?>
            <option value="<?php echo esc_attr( $page['id'] ) ?>" <?php selected( $page['id'], $current_page ) ?>>
				<?php echo esc_html( $page['title'] ) ?>
            </option>
		<?php endforeach; ?>
    </select>
	<?php
	return ob_get_clean();
}


//format time
function sts_time_ago( $time_ago ) {
	$cur_time     = time();
	$time_elapsed = $cur_time - $time_ago;
	$seconds      = $time_elapsed;
	$minutes      = round( $time_elapsed / 60 );
	$hours        = round( $time_elapsed / 3600 );
// Seconds
	if ( $seconds <= 60 ) {
		if ( $seconds < 5 ) {
			$text = esc_html__( "just now", "sts" );
		} else {
			$text = sprintf( esc_html__( "%d seconds ago", "sts" ), $seconds );
		}
	} //Minutes
	else if ( $minutes <= 60 ) {
		if ( $minutes == 1 ) {
			$text = "about one minute ago";
		} else {
			$text = sprintf( esc_html__( "about %d minutes ago", 'sts' ), $minutes );
		}
	} //Hours
	else if ( $hours <= 24 ) {
		if ( $hours == 1 ) {
			$text = "about an hour ago";
		} else {
			$text = sprintf( esc_html__( "about %d hours ago", 'sts' ), $hours );
		}
	} //Days
	else {
		$text = date( 'M d,Y H:i:s', $time_ago );
	}

	return $text;
}

//substring without break word
function sts_truncate_str( $text, $max_length ) {
	$tags   = array();
	$result = "";

	$is_open          = false;
	$grab_open        = false;
	$is_close         = false;
	$in_double_quotes = false;
	$in_single_quotes = false;
	$tag              = "";

	$i        = 0;
	$stripped = 0;

	$stripped_text = strip_tags( $text );

	while ( $i < strlen( $text ) && $stripped < strlen( $stripped_text ) && $stripped < $max_length ) {
		$symbol = $text{$i};
		$result .= $symbol;

		switch ( $symbol ) {
			case '<':
				$is_open   = true;
				$grab_open = true;
				break;

			case '"':
				if ( $in_double_quotes ) {
					$in_double_quotes = false;
				} else {
					$in_double_quotes = true;
				}

				break;

			case "'":
				if ( $in_single_quotes ) {
					$in_single_quotes = false;
				} else {
					$in_single_quotes = true;
				}

				break;

			case '/':
				if ( $is_open && ! $in_double_quotes && ! $in_single_quotes ) {
					$is_close  = true;
					$is_open   = false;
					$grab_open = false;
				}

				break;

			case ' ':
				if ( $is_open ) {
					$grab_open = false;
				} else {
					$stripped ++;
				}

				break;

			case '>':
				if ( $is_open ) {
					$is_open   = false;
					$grab_open = false;
					array_push( $tags, $tag );
					$tag = "";
				} else if ( $is_close ) {
					$is_close = false;
					array_pop( $tags );
					$tag = "";
				}

				break;

			default:
				if ( $grab_open || $is_close ) {
					$tag .= $symbol;
				}

				if ( ! $is_open && ! $is_close ) {
					$stripped ++;
				}
		}

		$i ++;
	}
	if ( $tags ) {
		$result .= "</" . array_pop( $tags ) . ">";
	}

	return $result;
}


/**
 * Get support url with endpoint
 *
 * @param string $endpoint
 *
 * @return string
 */
function sts_support_page_url( $endpoint = '' ) {
	return trailingslashit( get_permalink( sts_support_page_id() ) ) . $endpoint;
}

/**
 * Send json
 *
 * @param $data
 * @param $status
 * @param null $status_code
 */
function sts_send_json( $data, $status, $status_code = null ) {
	$response = array(
		'status' => $status,
		'data'   => $data
	);
	wp_send_json( $response, $status_code );
}

/**
 * @param $ticket_id
 *
 * @return string|void
 */
function sts_link_ticket( $ticket_id ) {
	return sts_support_page_url( 'ticket-details/?t=' . $ticket_id );
}

/**
 * @param $email
 * @param $subject
 * @param $message
 *
 * @return bool
 */
function sts_send_mail( $email, $subject, $message ) {
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$sent    = wp_mail( $email, $subject, $message, $headers );

	return $sent;
}

/**
 * @param $str
 * @param array $param
 * @param array $replacement
 *
 * @return mixed
 */
function sts_replace_content( $str, $param = array(), $replacement = array() ) {
	$result = $str;
	for ( $i = 0; $i < count( $param ); $i ++ ) {
		$len_param = strlen( $param[ $i ] );
		if ( stripos( $str, $param[ $i ] ) !== false ) {
			$result = substr_replace( $result, $replacement[ $i ], stripos( $result, $param[ $i ] ), $len_param );
		}
	}

	return $result;
}

function sts_create_file_download( $folder, $name, $version ) {
	$file_name = str_replace( ' ', '_', $name );
	$file_name = $file_name . '_' . $version . '.zip';

	return $folder . '/' . $file_name;
}

/**
 *  Check current user is moderator: administrator, leader supporter, supporter
 */
function sts_user_is_moderator() {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	$current_user = wp_get_current_user();

	return
		( user_can( $current_user, 'administrator' )
		  || user_can( $current_user, 'leader_supporter' )
		  || user_can( $current_user, 'supporter' ) );
}

/**
 * Resize image
 *
 * @param $image
 * @param $max_width
 * @param $max_height
 *
 * @return bool|string|WP_Error|WP_Image_Editor
 *
 */
function sts_resize_image_max( $image, $max_width, $max_height ) {
	$upload_dir       = wp_get_upload_dir();
	$image_path       = pathinfo( $image );
	$image_upload_dir = $upload_dir['basedir'];
	$image_full_path  = $image_upload_dir . $image;
	$new_file_name    = str_replace( $image, $image_path['filename'], $image_path['filename'] . '_' . $max_width . 'x' . $max_height . '.' . $image_path['extension'] );
	$new_file_url     = $upload_dir['baseurl'] . '/' . $image_path['dirname'] . '/' . $new_file_name;
	$new_file_path    = $image_upload_dir . $image_path['dirname'] . '/' . $new_file_name;
	$image_url        = $upload_dir['baseurl'] . $image_path['dirname'] . '/' . $image_path['basename'];
	if ( strripos( $image_path['dirname'], $upload_dir['baseurl'] ) !== false ) {
		$image_upload_dir = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $image_path['dirname'] ) . '/';
		$image_full_path  = $image_upload_dir . $image_path['basename'];
		$new_file_url     = $image_path['dirname'] . '/' . $new_file_name;
		$new_file_path    = $image_upload_dir . $new_file_name;
		$image_url        = $image;
	}
	if ( file_exists( $new_file_path ) ) {
		return $new_file_url;
	}
	if ( ! file_exists( $image_full_path ) ) {
		return false;
	}
	$image_size = getimagesize( $image_full_path );

	$w = $image_size[0];
	$h = $image_size[1];
	if ( ( ! $w ) || ( ! $h ) ) {
		return false;
	}
	if ( ( $w <= $max_width ) && ( $h <= $max_height ) ) {
		return $image_url;
	}
	$image = wp_get_image_editor( $image_full_path );
	if ( ! is_wp_error( $image ) ) {
		$image->resize( $max_width, $max_height, true );
		$image->save( $new_file_path );

		return $new_file_url;
	}

	return false;
}

function sts_get_avatar( $user_id, $max_width, $max_height ) {
	$attachment = STS()->db()->attachments()->get_user_attachment_by_user_id( $user_id );
	$avatar     = get_avatar_url( $user_id );
	if ( $attachment ) {
		$new_file_path = sts_resize_image_max( $attachment->attachment_url, $max_width, $max_height );
		if ( $new_file_path !== false ) {
			$avatar = $new_file_path;
		}
	}

	return $avatar;
}

function sts_user_is_locked( $user_id ) {
	$is_lock = get_user_meta( $user_id, 'sts_is_lock', true );
	if ( $is_lock == 1 ) {
		return true;
	}

	return false;
}

function sts_logout_locked( $user_id ) {
	$is_lock = sts_user_is_locked( $user_id );
	if ( $is_lock ) {
		sts_send_json(
			sts_support_page_url( 'login/?login=locked' ), 'redirect' );
		exit();
	}
}

function sts_get_user_id_locked() {
	$users_locked = get_users( array(
		'role'       => 'subscriber',
		'meta_key'   => 'sts_is_lock',
		'meta_value' => '1',
	) );
	$user_ids     = array();
	$user_id_str  = '0';
	if ( $users_locked ) {
		foreach ( $users_locked as $user_locked ) {
			$user_ids[]  = $user_locked->ID;
			$user_id_str = implode( ',', $user_ids );
		}
	}

	return $user_id_str;
}

function sts_set_updating_date( $ticket_id ) {
	$ticket        = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	$updating_date = $ticket->updating_date;
	if ( $ticket->status == 1 || $ticket->status == 2 ) {
		if ( ! is_null( $ticket->latest_updating_date ) ) {
			$updating_date = $ticket->latest_updating_date;
		}
	} elseif ( $ticket->status == 3 ) {
		$updating_date = $ticket->close_date;
	}

	return $updating_date;
}

function sts_set_ticket( $tickets = array() ) {
	$contents = array();
	foreach ( $tickets as $ticket ) {
		$customer_id = $ticket->customer_id;
		$customer    = get_user_by( 'ID', $customer_id );
		$expired     = 0;
		if ( $customer ) {
			$numberMessage = STS()->db()->messages()->count_message_by_ticket_id( $ticket->id );
			$supporter     = get_user_by( 'ID', $ticket->supporter_id );

			if ( has_filter( 'sts_get_theme' ) ) {
				$info       = apply_filters( 'sts_get_theme', $ticket );
				$theme      = $info['theme'];
				$expired    = $info['expired'];
				$theme_link = sts_support_page_url( 'tickets?theme_id=' . $theme->theme_id );

			} else {
				$theme      = apply_filters( 'sts_get_categories', $ticket );
				$theme_link = sts_support_page_url( 'tickets?cat_id=' . $theme->theme_id );
			}
			$updating_date  = sts_set_updating_date( $ticket->id );
			$user_lock_name = '';
			if ( $ticket->is_lock == 1 ) {
				$user_lock      = get_user_by( 'ID', $ticket->user_lock );
				$user_lock_name = $user_lock->display_name;
			}
			$contents[] = array(
				'id'             => $ticket->id,
				'avatar'         => sts_get_avatar( $customer->ID, 100, 100 ),
				'name'           => $customer->display_name,
				'ticket_link'    => sts_link_ticket( $ticket->id ),
				'subject'        => $ticket->subject,
				'updating_date'  => sts_time_ago( strtotime( $updating_date ) ),
				'numberMessage'  => $numberMessage,
				'status'         => $ticket->status,
				'rate'           => $ticket->rate,
				'customer_id'    => $customer_id,
				'supporter_name' => $supporter ? sprintf( esc_html__( '%s processing', 'sts' ), $supporter->display_name ) : '',
				'user_lock'      => $user_lock_name,
				'expired'        => $expired,
				'theme_name'     => $theme->theme_name,
				'theme_link'     => $theme_link,
			);
		}
	}

	return $contents;
}

function sts_attachment_url( $attachment_url ) {

	$attachment_info    = pathinfo( $attachment_url );
	$upload_dir         = wp_get_upload_dir();
	$attachment_url_new = $upload_dir['baseurl'] . $attachment_info['dirname'] . '/' . $attachment_info['basename'];
	if ( strripos( $attachment_info['dirname'], $upload_dir['baseurl'] ) !== false ) {
		$attachment_url_new = $attachment_info['dirname'] . '/' . $attachment_info['basename'];
	}

	return $attachment_url_new;

}

function sts_get_guid() {
	if ( function_exists( 'com_create_guid' ) ) {
		return trim( com_create_guid(), '{}' );
	} else {
		mt_srand( (double) microtime() * 10000 );//optional for php 4.2.0 and up.
		$charid = strtolower( md5( uniqid( rand(), true ) ) );
		$hyphen = chr( 45 );// "-"
		$uuid   = substr( $charid, 0, 8 ) . $hyphen
		          . substr( $charid, 8, 4 ) . $hyphen
		          . substr( $charid, 12, 4 ) . $hyphen
		          . substr( $charid, 16, 4 ) . $hyphen
		          . substr( $charid, 20, 12 );

		return $uuid;
	}
}

/**
 * Redirect to page current when not login
 */
function sts_redirect_not_login() {
	$current_user = wp_get_current_user();
	global $wp;
	$current_url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
	if ( ! $current_user->exists() ) {
		wp_redirect( sts_support_page_url( 'login/?sts_redirect=' . rawurlencode( $current_url ) ) );
		exit();
	}
}
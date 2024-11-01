<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['sts_ticket_detail_nonce_field'] ) &&
     wp_verify_nonce( $_POST['sts_ticket_detail_nonce_field'], 'processing-ticket-details' )
) {
	$ticket_id = absint( sanitize_text_field( $_POST['ticketID'] ) );
	if ( $ticket_id === 0 ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-reply-ticket',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
	if ( is_null( $ticket ) ) {
		sts_send_json(
			array(
				'target'  => '#sts-message-reply-ticket',
				'message' => esc_html__( 'Error', 'sts' )
			),
			'alert' );
	}
	$current_user = wp_get_current_user();
	$user_meta    = get_userdata( $current_user->ID );
	$user_roles   = $user_meta->roles;
	sts_logout_locked( $current_user->ID );
	if ( ( $current_user->ID == $ticket->customer_id || user_can( $current_user, 'administrator' )
	       || ( ( user_can( $current_user, 'leader_supporter' )
	              || user_can( $current_user, 'supporter' ) )
	            && ( $ticket->is_lock == 1 && $ticket->user_lock == $current_user->ID ) || is_null( $ticket->user_lock ) ) ) && $ticket->status != 3 ) {
		global $wpdb;
		$error        = '';
		$table_prefix = STS()->db()->table_prefix();
		$message      = wpautop( stripslashes( wp_filter_post_kses( $_POST['message'] ) ) );
		if ( empty( $message ) ) {
			$error = esc_html__( "Please type your message!!", 'sts' );
		} else {
			$files         = $_FILES['attachment'];
			$date          = date( "Y-m-d H:i:s" );
			$table_name    = $table_prefix . 'message';
			$ticket        = $ticket = STS()->db()->tickets()->get_ticket_by_id( $ticket_id );
			$result_upload = sts_upload_file( $files );
			$error         = $result_upload['error'];
			$file_arr      = $result_upload['file_arr'];
			if ( $error == '' ) {
				$result = $wpdb->insert(
					$table_name,
					array(
						'message'      => $message,
						'created_date' => $date,
						'user_id'      => $current_user->ID,
						'ticket_id'    => $ticket_id,
						'is_question'  => false
					)
				);
				if ( $result !== false && $result !== 0 ) {
					$messageID = $wpdb->insert_id;
					if ( user_can( $current_user, 'leader_supporter' )
					     || user_can( $current_user, 'supporter' )
					     || user_can( $current_user, 'administrator' ) ) {
						$status     = 2;
						$nb_message = STS()->db()->messages()->get_supporters_of_message( $ticket_id );
						if ( $nb_message && ( is_null( $ticket->supporter_id ) || $ticket->supporter_id == '' ) ) {
							$wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set supporter_id=%d,assigned_date=%s where id=%d",
								$nb_message->user_id, $date, $ticket_id ) );
						}
					} elseif ( $current_user->ID == $ticket->customer_id ) {
						$status = 1;
					}
					if ( ( user_can( $current_user, 'administrator' ) && $current_user->ID != $ticket->user_lock ) || user_can( $current_user, 'subscriber' ) ) {
						$wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set status=%d,updating_date=%s,latest_updating_date=%s where id=%d",
							$status, $date, $date, $ticket_id ) );
					} else {
						$wpdb->query( $wpdb->prepare( "UPDATE {$table_prefix}ticket set status=%d,updating_date=%s,latest_updating_date=%s,is_lock=0,lock_date=NULL,user_lock=NULL where id=%d",
							$status, $date, $date, $ticket_id ) );
					}
					if ( ! empty( array_filter( $file_arr ) ) ) {
						$table_name = $table_prefix . 'attachment';
						foreach ( $file_arr as $f ) {
							$wpdb->insert(
								$table_name,
								array(
									'attachment_name' => $f['name'],
									'attachment_url'  => $f['url'],
									'message_id'      => $messageID,
								)
							);
						}
					}
					$user_followings = STS()->db()->tickets()->get_user_follow_ticket( $ticket_id );
					$userIDs         = array();
					$userIDs[]       = $ticket->customer_id;
					if ( ! is_null( $ticket->supporter_id ) && $ticket->supporter_id != '' ) {
						$userIDs[] = $ticket->supporter_id;
					}
					$user_notifis = array_unique( array_merge( $user_followings, $userIDs ) );
					$content      = '<strong>' . $current_user->display_name . '</strong>' . esc_html__( " has just reply ticket", "sts" ) . '<strong> ' . $ticket->subject . '</strong>';
					STS()->db()->notification()->sts_send_notification( $content, $user_notifis, $ticket_id );
					if ( $ticket->customer_id != $current_user->ID ) {
						$customer        = get_user_by( 'ID', $ticket->customer_id );
						$is_receive_mail = get_metadata( 'user', $ticket->customer_id, 'sts_is_receive_mail', true );
						if ( $is_receive_mail == 1 ) {
							$template = STS()->db()->mail_template()->get_mail_template_by_key( 'mail_reply' );
							if ( $template ) {
								$subject      = $template->subject;
								$content_mail = sts_replace_content( $template->content,
									array( '%customer_name', '%user_reply_name', '%ticket_url', '%url_unsubscribe' ),
									array(
										$customer->display_name,
										$current_user->display_name,
										'<a href="' . esc_url( sts_link_ticket( $ticket_id ) . '#message-' . $messageID ) . '" style="color:#039be5;">' . $ticket->subject . '</a>',
										'<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $ticket->customer_id . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
									)
								);
							} else {
								$subject = esc_html__( 'Supporter reply ticket', 'sts' );
								ob_start();
								STS()->get_template( 'mails/template-mail-reply.php',
									array(
										'customer_name'   => $customer->display_name,
										'user_name'       => $current_user->display_name,
										'ticket_url'      => '<a href="' . esc_url( sts_link_ticket( $ticket_id ) . '#message-' . $messageID ) . '" style="color:#039be5;">' . $ticket->subject . '</a>',
										'unsubscribe_url' => '<a href="' . esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $ticket->customer_id . '&unsubscribe=false' ) ) . '">' . esc_html__( 'unsubscribe', 'sts' ) . '</a>'
									) );
								$content_mail = ob_get_clean();
							}
							$sent = sts_send_mail( $customer->user_email, $subject, $content_mail );
							if ( ! $sent ) {
								global $phpmailer;
								if ( isset( $phpmailer ) ) {
									error_log( $phpmailer->ErrorInfo );
								}
							}
						}
					}
				} else {
					$error = esc_html__( "Can not reply this ticket!!", 'sts' );
				}
			}
		}

	} else {
		$error = esc_html__( "You can not reply this ticket", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json(
		sts_support_page_url( "/ticket-details?t=" . $ticket_id ),
		'redirect' );

} else {
	sts_send_json(
		array(
			'target'  => '#sts-message-reply-ticket',
			'message' => $error
		),
		'alert' );
}
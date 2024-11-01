<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( isset( $_POST['nonce'] ) && check_ajax_referer( 'sts_get_notification_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) || user_can( $current_user, 'subscriber' ) ) {
		$limit           = STS()->db()->notification()->limit_notification;
		$nb_notification = STS()->db()->notification()->count_notification_by_user( $current_user->ID );
		$current_page    = absint( sanitize_text_field( $_POST['current_page'] ) );
		if ( $current_page === 0 ) {
			$current_page = 1;
		}
		$offset   = $limit * ( $current_page - 1 );
		$numbPage = ceil( ( $nb_notification ) / $limit );
		if ( $numbPage >= $current_page ) {
			$notifications = STS()->db()->notification()->get_notification_by_user( $current_user->ID, $offset );
			if ( $notifications ) {
				$contents = array();
				foreach ( $notifications as $notification ) {
					$contents[] = array(
						'link'         => $notification->link,
						'content'      => $notification->content,
						'created_date' => $notification->created_date
					);
				}
				ob_start();
				STS()->get_template( 'notifications/notification-item.php',
					array(
						'notifications' => $contents
					) );
				$content = ob_get_clean();
			} else {
				$content = '<div class="sts-no-content">' . esc_html__( 'No data!!', 'sts' ) . '</div>';
			}
			$current_page = $current_page + 1;
		} else {
			$content = '';
		}


	} else {
		$error = esc_html__( "You can not get this message", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( array(
		'content'             => $content,
		'target'              => '.notification__container',
		'target_current_page' => '#notification-current-page',
		'current_page'        => $current_page
	), 'append' );
} else {
	sts_send_json(
		array(
			'target'  => '#notification-message',
			'message' => $error
		),
		'alert' );
}

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_supporter_report_show_message_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' ) ) {
		$limit        = STS()->db()->messages()->limit_message;
		$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
		if ( empty( $current_page ) || $current_page === 0 ) {
			$current_page = 1;
		}
		$offset       = $limit * ( $current_page - 1 );
		$nb_message   = absint( sanitize_text_field( $_POST['nb_message'] ) );
		$numbPage     = ceil( $nb_message / $limit );
		$supporter_id = absint( sanitize_text_field( $_POST['supporter'] ) );
		$from_date    = sanitize_text_field( $_POST['fromDate'] );
		$to_date      = sanitize_text_field( $_POST['toDate'] );
		$paginator    = '';
		$messages     = STS()->db()->messages()->get_message_by_supporter( $supporter_id, $from_date, $to_date, $offset );
		if ( $messages ) {
			$contents = array();
			foreach ( $messages as $message ) {
				$contents[] = array(
					'id'      => $message->id,
					'message' => sts_truncate_str( $message->message, 100 ),
					'link'    => sts_link_ticket( $message->ticket_id ) . '#message-' . $message->id
				);
			}
			ob_start();
			STS()->get_template( 'reports/message-item.php', array(
				'messages' => $contents,
				'nonce'    => wp_create_nonce( 'sts_frontend_delete_template' )
			) );
			$content = ob_get_clean();
			if ( $numbPage > 1 ) {
				ob_start();
				STS()->get_template( 'paginator.php',
					array(
						'numbPage'     => $numbPage,
						'current_page' => $current_page,
						'target'       => '#form-filter-listing-message',
						'limit'        => $limit,
						'total'        => $nb_message
					) );
				STS()->get_template( 'reports/form-message-paginator.php',
					array(
						'from_date'    => $from_date,
						'to_date'      => $to_date,
						'nb_message'   => $nb_message,
						'supporter_id' => $supporter_id,
						'current_page' => $current_page
					) );
				$paginator = ob_get_clean();
			}

			$arr_param = array(
				'target'            => 'table.listing-message>tbody',
				'content'           => $content,
				'current_page'      => intval( $current_page ),
				'total_page'        => $numbPage,
				'target_paginator'  => '.listing-message-paginator',
				'content_paginator' => $paginator,
				'open_content'      => '.block-listing-message'
			);
		} else {
			$arr_param = array(
				'target'            => 'table.listing-message>tbody',
				'content'           => '<div class="sts-no-content">' . esc_html__( 'No message found!!', 'sts' ) . '</div>',
				'open_content'      => '.block-listing-message',
				'target_paginator'  => '.listing-message-paginator',
				'content_paginator' => $paginator,
			);
		}
	} else {
		$error = esc_html__( "You have not permission to do this action!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {
	sts_send_json( $arr_param
		, 'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
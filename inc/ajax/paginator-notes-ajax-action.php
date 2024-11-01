<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_note_paginator_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) ) {
		$limit        = STS()->db()->notes()->limit_note;
		$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
		if ( $current_page === 0 ) {
			$current_page = 1;
		}
		$offset   = $limit * ( $current_page - 1 );
		$notes    = STS()->db()->notes()->get_notes_by_user( $current_user->ID, $offset );
		$nb_note  = STS()->db()->notes()->count_notes_by_user( $current_user->ID );
		$numbPage = ceil( $nb_note / $limit );
		if ( $notes ) {
			$contents = array();
			foreach ( $notes as $note ) {
				$contents[] = array(
					'url'          => sts_link_ticket( $note->ticket_id ),
					'message'      => strlen( $note->message ) > 50 ? sts_truncate_str( $note->message, 50 ) . '...' : $note->message,
					'created_date' => date( 'F d,Y', strtotime( $note->created_date ) )
				);
			}
			ob_start();
			STS()->get_template( 'dashboards/note-item-dashboard.php', array( 'notes' => $contents ) );
			$content = ob_get_clean();
			ob_start();
			STS()->get_template( 'paginator.php', array(
				'numbPage'     => $numbPage,
				'current_page' => $current_page,
				'target'       => '#form-paginator-listing-note',
				'limit'        => $limit,
				'total'        => $nb_note
			) );
			$paginator = ob_get_clean();
			$arr_param = array(
				'target'            => '.note-items',
				'content'           => $content,
				'current_page'      => intval( $current_page ),
				'total_page'        => $numbPage,
				'target_paginator'  => '.listing-note-paginator',
				'content_paginator' => $paginator
			);
		} else {
			$content   = '<div class="sts-no-content">' . esc_html__( 'No note found!!', 'sts' ) . '</div>';
			$arr_param = array(
				'target'  => '.note-items',
				'content' => $content,
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
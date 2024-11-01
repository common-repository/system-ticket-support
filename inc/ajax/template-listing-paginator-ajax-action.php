<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_template_paginator_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' )
	     || user_can( $current_user, 'leader_supporter' )
	     || user_can( $current_user, 'supporter' ) ) {
		$limit        = STS()->db()->templates()->limit_template;
		$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
		if ( $current_page === 0 ) {
			$current_page = 1;
		}
		$offset = $limit * ( $current_page - 1 );
		if ( user_can( $current_user, 'administrator' )
		     || user_can( $current_user, 'leader_supporter' ) ) {
			$templates   = STS()->db()->templates()->get_all_template_limit( $offset );
			$nb_template = count( STS()->db()->templates()->get_all_template() );
		} else {
			$templates   = STS()->db()->templates()->getting_template_by_user_limit( $current_user->ID, $offset );
			$nb_template = count( STS()->db()->templates()->getting_template_by_user( $current_user->ID ) );
		}
		$numbPage = ceil( $nb_template / $limit );
		if ( $templates ) {
			$contents = array();
			foreach ( $templates as $template ) {
				$can_edit = 0;
				$can_lock = 0;
				if ( ( ( $template->is_public == 0 && $template->user_id == $current_user->ID ) && user_can( $current_user, 'supporter' ) )
				     || user_can( $current_user, 'administrator' )
				     || user_can( $current_user, 'leader_supporter' )
				) {
					$can_edit = 1;
				}
				if ( user_can( $current_user, 'administrator' )
				     || user_can( $current_user, 'leader_supporter' ) ) {
					$can_lock = 1;
				}
				$user_name = '';
				$user      = get_user_by( 'ID', $template->user_id );
				if ( $user ) {
					$user_name = $user->display_name;
				}
				$contents[] = array(
					'id'             => $template->id,
					'template_name'  => $template->template_name,
					'template_value' => $template->template_value,
					'can_edit'       => $can_edit,
					'is_public'      => $template->is_public,
					'can_lock'       => $can_lock,
					'user_name'      => $user_name
				);
			}
			ob_start();
			STS()->get_template( 'template/template-item.php', array(
				'templates' => $contents,
				'nonce'     => wp_create_nonce( 'sts_frontend_delete_template' )
			) );
			$content = ob_get_clean();
			ob_start();
			STS()->get_template( 'paginator.php',
				array(
					'numbPage'     => $numbPage,
					'current_page' => $current_page,
					'target'       => '#form-paginator-listing-template',
					'limit'        => $limit,
					'total'        => $nb_template
				) );
			$paginator = ob_get_clean();
			$arr_param = array(
				'target'            => 'table.table-templates>tbody',
				'content'           => $content,
				'current_page'      => intval( $current_page ),
				'total_page'        => $numbPage,
				'target_paginator'  => '.listing-template-paginator',
				'content_paginator' => $paginator
			);
		} else {
			$arr_param = array(
				'target'  => 'table.table-templates>tbody',
				'content' => '<div class="sts-no-content">' . esc_html__( 'No template found!!', 'sts' ) . '</div>'
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
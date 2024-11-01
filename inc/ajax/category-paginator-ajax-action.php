<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_category_paginator_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	sts_logout_locked( $current_user->ID );
	if ( user_can( $current_user, 'administrator' ) ) {
		$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
		if ( $current_page === 0 ) {
			$current_page = 1;
		}
		$limit      = STS()->db()->themes()->limit_theme;
		$offset     = $limit * ( $current_page - 1 );
		$numb_theme = count( STS()->db()->themes()->getting_theme_active() );
		$numbPage   = ceil( $numb_theme / $limit );
		$themes     = STS()->db()->themes()->get_theme_limit( $offset );
		if ( $themes ) {
			$contents = array();
			foreach ( $themes as $theme ) {
				$nb_ticket  = STS()->db()->tickets()->count_ticket_by_theme( $theme->theme_id );
				$contents[] = array(
					'id'         => $theme->id,
					'theme_name' => $theme->theme_name,
					'status'     => $theme->status,
					'theme_id'   => $theme->theme_id,
					'nb_ticket'  => $nb_ticket
				);
			}
			ob_start();
			STS()->get_template( 'category/category-item.php',
				array(
					'themes' => $contents,
					'nonce'  => wp_create_nonce( 'sts_frontend_delete_category' )
				) );
			$content = ob_get_clean();
			ob_start();
			STS()->get_template( 'paginator.php',
				array(
					'numbPage'     => $numbPage,
					'current_page' => $current_page,
					'target'       => '#form-paginator-category',
					'limit'        => $limit,
					'total'        => $numb_theme
				) );
			$paginator = ob_get_clean();
			$arr_param = array(
				'target'            => 'table.listing-theme>tbody',
				'content'           => $content,
				'current_page'      => intval( $current_page ),
				'total_page'        => $numbPage,
				'target_paginator'  => '.theme-paginator',
				'content_paginator' => $paginator
			);
		} else {
			$arr_param = array(
				'target'  => 'table.listing-theme>tbody',
				'content' => '<div class="sts-no-content">' . esc_html__( 'No category found!!', 'sts' ) . '</div>'
			);
		}

	} else {
		$error = esc_html__( "You have not permission to do this action", 'sts' );
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
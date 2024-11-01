<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$error = '';
if ( check_ajax_referer( 'sts_report_filter_security', 'nonce' ) ) {
	$current_user = wp_get_current_user();
	if ( user_can( $current_user, 'administrator' ) ) {
		$nb_ticket_satisfied   = 0;
		$nb_ticket_unsatisfied = 0;
		$tickets_satisfied     = array();
		$tickets_unsatisfied   = array();
		$from_date             = sanitize_text_field( $_POST['fromDate'] );
		$to_date               = sanitize_text_field( $_POST['toDate'] );
		$rate                  = sanitize_text_field( $_POST['rating'] );
		$supporter             = sanitize_text_field( $_POST['supporter'] );
		if ( isset( $_POST['current_page'] ) ) {
			$current_page = absint( sanitize_text_field( $_POST['current_page'] ) );
			if ( $current_page === 0 ) {
				$current_page = 1;
			}
		} else {
			$current_page = 1;
		}
		$limit                 = STS()->db()->tickets()->limit_ticket;
		$offset                = $limit * ( $current_page - 1 );
		$nb_ticket             = STS()->db()->tickets()->report( $from_date, $to_date, $rate, $supporter, $offset );
		$nb_ticket_satisfied   = $nb_ticket['satisfied'];
		$nb_ticket_unsatisfied = $nb_ticket['unsatisfied'];
		$nb_ticket_visited     = $nb_ticket['nb_ticket_visited_not_rating'];
		$nb_ticket_other       = $nb_ticket['nb_ticket_other'];
		$content_paginator     = '';
		$target_paginator      = '';
		$numbPage              = 0;
		$nb_current_ticket     = 0;

		ob_start();
		STS()->get_template( 'reports/satisfied-link.php',
			array(
				'nb_ticket_satisfied' => $nb_ticket_satisfied,
				'form_date'           => $from_date,
				'to_date'             => $to_date,
				'supporter'           => $supporter
			) );
		$content_satisfied = ob_get_clean();
		ob_start();
		STS()->get_template( 'reports/unsatisfied-link.php',
			array(
				'nb_ticket_unsatisfied' => $nb_ticket_unsatisfied,
				'form_date'             => $from_date,
				'to_date'               => $to_date,
				'supporter'             => $supporter
			) );
		$content_unsatisfied = ob_get_clean();
		ob_start();
		STS()->get_template( 'reports/visited-link.php',
			array(
				'nb_ticket_visited_not_rating' => $nb_ticket_visited,
				'form_date'                    => $from_date,
				'to_date'                      => $to_date,
				'supporter'                    => $supporter
			) );
		$content_visited = ob_get_clean();
		ob_start();
		STS()->get_template( 'reports/other-link.php',
			array(
				'nb_ticket_other' => $nb_ticket_other,
				'form_date'       => $from_date,
				'to_date'         => $to_date,
				'supporter'       => $supporter
			) );
		$content_other = ob_get_clean();
		if ( $rate == '1' ) {
			$content_unsatisfied = '';
			$content_visited     = '';
			$content_other       = '';
			$nb_ticket_visited   = 0;
			$nb_ticket_other     = 0;

		}
		if ( $rate == '0' ) {
			$content_satisfied = '';
			$content_visited   = '';
			$content_other     = '';
			$nb_ticket_visited = 0;
			$nb_ticket_other   = 0;
		}

		if ( isset( $_POST['is_click'] ) && $_POST['is_click'] == '1' ) {
			$target = '.listing-ticket__items';
			if ( isset( $_POST['link_visited'] ) ) {
				$tickets           = $nb_ticket['tickets_visited_not_rating'];
				$nb_current_ticket = $nb_ticket_visited;
				$current_link      = 'visited';
			} elseif ( isset( $_POST['link_other'] ) ) {
				$tickets           = $nb_ticket['tickets_other'];
				$nb_current_ticket = $nb_ticket_other;
				$current_link      = 'other';
			} elseif ( isset( $_POST['link_satisfied'] ) ) {
				$tickets           = $nb_ticket['tickets'];
				$nb_current_ticket = $nb_ticket_satisfied;
				$current_link      = 'satisfied';
			} else {
				$tickets           = $nb_ticket['tickets'];
				$nb_current_ticket = $nb_ticket_unsatisfied;
				$current_link      = 'unsatisfied';
			}
			if ( $tickets ) {
				$contents = array();
				foreach ( $tickets as $ticket ) {
					$customer_id = $ticket->customer_id;
					$customer    = get_user_by( 'ID', $customer_id );
					if ( $customer ) {
						$numberMessage = STS()->db()->messages()->count_message_by_ticket_id( $ticket->id );
						$contents[]    = array(
							'id'            => $ticket->id,
							'avatar'        => sts_get_avatar( $customer->ID, 100, 100 ),
							'name'          => $customer->display_name,
							'customer_id'   => $customer_id,
							'ticket_link'   => sts_link_ticket( $ticket->id ),
							'subject'       => $ticket->subject,
							'updating_date' => sts_time_ago( strtotime( $ticket->updating_date ) ),
							'numberMessage' => $numberMessage,
							'rate'          => $ticket->rate,
							'rate_info'     => $ticket->rate_info
						);
					}
				}
				ob_start();
				STS()->get_template( 'tickets/ticket-item-report.php', array( 'contents' => $contents ) );
				$content_tickets = ob_get_clean();
				if ( $current_link = 'satisfied' && $nb_current_ticket > $limit ) {
					$numbPage    = ceil( $nb_current_ticket / $limit );
					$nb_ticket   = $nb_current_ticket;
					$target_form = '#sts-report-paginator-satisfied';
				}
				if ( $current_link = 'unsatisfied' && $nb_current_ticket > $limit ) {
					$numbPage    = ceil( $nb_current_ticket / $limit );
					$nb_ticket   = $nb_current_ticket;
					$target_form = '#sts-report-paginator-unsatisfied';
				}
				if ( $current_link = 'visited' && $nb_current_ticket > $limit ) {
					$numbPage    = ceil( $nb_current_ticket / $limit );
					$nb_ticket   = $nb_current_ticket;
					$target_form = '#sts-report-paginator-visited';
				}
				if ( $current_link = 'other' && $nb_current_ticket > $limit ) {
					$numbPage    = ceil( $nb_current_ticket / $limit );
					$nb_ticket   = $nb_current_ticket;
					$target_form = '#sts-report-paginator-other';
				}
				if ( $numbPage > 1 ) {
					ob_start();
					STS()->get_template( 'paginator.php', array(
						'numbPage'     => $numbPage,
						'offset'       => 0,
						'current_page' => 1,
						'target'       => $target_form,
						'limit'        => $limit,
						'total'        => $nb_ticket
					) );
					$content_paginator = ob_get_clean();
				} else {
					$content_paginator = '';
				}

			} else {
				$content_tickets = '<div class="sts-no-content">' . esc_html__( 'No ticket found!!', 'sts' ) . '</div>';
			}
			$open_content  = '.report-ticket';
			$close_content = '';
			$target_chart  = '';
			$content_chart = '';
		} else {
			$target          = '';
			$content_tickets = '';
			$open_content    = '';
			$close_content   = '.report-ticket';
			$target_chart    = '.report__chart-chart';
			$content_chart   = '<canvas id="reportChart" width="400" height="200"></canvas>';
		}
	} else {
		$error = esc_html__( "You have not permission to do this action!!", 'sts' );
	}
} else {
	$error = esc_html__( "Error", 'sts' );
}
if ( $error == '' ) {

	sts_send_json(
		array(
			'target'                => $target,
			'content'               => $content_tickets,
			'target_satisfied'      => '#sts-report-legend-satisfied',
			'content_satisfied'     => $content_satisfied,
			'target_unsatisfied'    => '#sts-report-legend-unsatisfied',
			'content_unsatisfied'   => $content_unsatisfied,
			'target_visited'        => '#sts-report-legend-visited',
			'content_visited'       => $content_visited,
			'target_other'          => '#sts-report-legend-other',
			'content_other'         => $content_other,
			'nb_ticket_satisfied'   => $nb_ticket_satisfied,
			'nb_ticket_unsatisfied' => $nb_ticket_unsatisfied,
			'nb_ticket_visited'     => $nb_ticket_visited,
			'nb_ticket_other'       => $nb_ticket_other,
			'close_content'         => $close_content,
			'open_content'          => $open_content,
			'target_chart'          => $target_chart,
			'content_chart'         => $content_chart,
			'target_paginator'      => '.sts-report-paginator',
			'content_paginator'     => $content_paginator,
			'current_page'          => $current_page,
			'total_page'            => $numbPage
		),
		'update' );
} else {
	sts_send_json(
		array(
			'target'  => '.form__message',
			'message' => $error
		),
		'alert' );
}
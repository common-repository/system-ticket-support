<?php
$current_user = wp_get_current_user();
$user_meta    = get_userdata( $current_user->ID );
$user_roles   = $user_meta->roles;
if ( in_array( 'subscriber', $user_roles ) ) {
	$tickets              = STS()->db()->tickets()->get_newest_responded_ticket_by_customer( $current_user->ID );
	$all_tickets          = STS()->db()->tickets()->get_tickets_by_customer( '', $current_user->ID, 0 );
	$ticket_arr_request   = STS()->db()->tickets()->get_tickets_by_customer( 1, $current_user->ID, 0 );
	$ticket_arr_responded = STS()->db()->tickets()->get_tickets_by_customer( 2, $current_user->ID, 0 );
	$ticket_arr_close     = STS()->db()->tickets()->get_tickets_by_customer( 3, $current_user->ID, 0 );
} elseif ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) ||
           in_array( 'administrator', $user_roles ) ) {
	$ticket_arr_request   = STS()->db()->tickets()->get_all_tickets( 1, 0, $current_user->ID );
	$ticket_arr_responded = STS()->db()->tickets()->get_all_tickets( 2, 0, $current_user->ID );
	$ticket_arr_close     = STS()->db()->tickets()->get_all_tickets( 3, 0, $current_user->ID );
	$all_tickets          = STS()->db()->tickets()->get_all_tickets( '', 0, $current_user->ID );
	$tickets              = STS()->db()->tickets()->get_older_ticket( 'all', $current_user->ID );

}
$numbTicket_all       = $all_tickets['all_tickets'];
$numbTicket_request   = $ticket_arr_request['all_tickets'];
$numbTicket_responded = $ticket_arr_responded['all_tickets'];
$numbTicket_close     = $ticket_arr_close['all_tickets'];
?>
<div class="dashboard">
    <div class="page-2column">
        <div class="sts-page-banner dashboard__banner">
            <div class="container-fluid">
                <div class="sts-page-banner-content">
                    <h3 class="sts-page-title dashboard__banner-title">
						<?php esc_html_e( 'Dashboards', 'sts' ) ?>
                    </h3>
					<?php
					$is_verified = get_user_meta( $current_user->ID, 'sts_is_verified', true );
					if ( $is_verified != '' && $is_verified == 0 ): ?>
                        <div class="sts-warning banner-error">
							<?php esc_html_e( 'Your account has not verified. Please confirm your mail to verify it!!', 'sts' ) ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
        <div class="sts-page-main mt-30">
            <div class="container-fluid">
                <div class="form__message"></div>
                <div class="page-2column__wrapper">
                    <div class="page-2column__main">
                        <div class="dashboard__container">
                            <div class="dashboard__report">
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets' ) ) ?>"
                                   class="dashboard__report-item">
                                    <div class="dashboard__report-item-wrapper">
                                        <div class="dashboard__report-header">
                                            <span class="dashboard__report-title"><?php esc_html_e( 'All', 'sts' ) ?></span>
                                        </div>
                                        <div class="dashboard__report-main">
                                            <span class="dashboard__report-number dashboard__report-number--all"><?php echo esc_html( $numbTicket_all ); ?></span>
                                            <span class="dashboard__report-name"><?php esc_html_e( 'Tickets', 'sts' ) ?></span>
                                        </div>
                                    </div>
                                </a>
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=request' ) ) ?>"
                                   class="dashboard__report-item">
                                    <div class="dashboard__report-item-wrapper">
                                        <div class="dashboard__report-header">
                                            <span class="dashboard__report-title"><?php esc_html_e( 'Request', 'sts' ) ?></span>
                                        </div>
                                        <div class="dashboard__report-main">
                                            <span class="dashboard__report-number dashboard__report-number--request">
                                                <?php echo esc_html( $numbTicket_request ); ?>
                                            </span>
                                            <span class="dashboard__report-name"><?php esc_html_e( 'Tickets', 'sts' ) ?></span>
                                        </div>
                                    </div>
                                </a>
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=responded' ) ) ?>"
                                   class="dashboard__report-item">
                                    <div class="dashboard__report-item-wrapper">
                                        <div class="dashboard__report-header">
                                            <span class="dashboard__report-title"><?php esc_html_e( 'Responded', 'sts' ) ?></span>
                                        </div>
                                        <div class="dashboard__report-main">
                                              <span class="dashboard__report-number dashboard__report-number--responded">
                                                  <?php echo esc_html( $numbTicket_responded ); ?>
                                              </span>
                                            <span class="dashboard__report-name"><?php esc_html_e( 'Tickets', 'sts' ) ?></span>
                                        </div>
                                    </div>
                                </a>
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=close' ) ) ?>"
                                   class="dashboard__report-item">
                                    <div class="dashboard__report-item-wrapper">
                                        <div class="dashboard__report-header">
                                            <span class="dashboard__report-title"><?php esc_html_e( 'Close', 'sts' ) ?></span>
                                        </div>
                                        <div class="dashboard__report-main">
                                            <span class="dashboard__report-number dashboard__report-number--close"><?php echo esc_html( $numbTicket_close ) ?></span>
                                            <span class="dashboard__report-name"><?php esc_html_e( 'Tickets', 'sts' ) ?></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="dashboards-distribution">
                                <div class="dashboards-distribution-item">
                                    <div class="sts-widget dashboards-distribution-item-wrapper">
                                        <div class="sts-widget-header dashboards-distribution-header">
                                            <span class="header-left"><?php esc_html_e( 'Ticket Distribution', 'sts' ) ?></span>
                                            <span class="header-right"><?php esc_html_e( 'Current', 'sts' ) ?></span>
                                        </div>
										<?php
										if ( $numbTicket_all == 0 ):
											?>
                                            <div class="text-center">
												<?php
												esc_html_e( 'Current no have ticket!', 'sts' );
												?>
                                            </div>
										<?php
										else:
											?>
                                            <div class="sts-widget-content dashboards-distribution-content dashboard__chart">
                                                <canvas id="myChart" height="336" width="262"></canvas>
                                                <div class="chart-legend"></div>
                                            </div>
										<?php
										endif;
										?>
                                    </div>
                                </div>
                                <div class="dashboards-distribution-item">
                                    <div class="sts-widget dashboards-distribution-item-wrapper">
                                        <div class="sts-widget-header dashboards-distribution-header">
											<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) ||
											           in_array( 'administrator', $user_roles ) ): ?>
                                                <div class="header-left"><?php esc_html_e( 'Older Ticket Request', 'sts' ) ?></div>
											<?php elseif ( in_array( 'subscriber', $user_roles ) ): ?>
                                                <div class="header-left"><?php esc_html_e( 'Newest Ticket Responded', 'sts' ) ?></div>
											<?php endif; ?>
                                            <div class="header-right">
												<?php if ( in_array( 'subscriber', $user_roles ) ): ?>
													<?php esc_html_e( 'Current', 'sts' ) ?>
												<?php else: ?>
                                                    <div class="dropdown">
                                                        <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                                           aria-expanded="false"
                                                           class="dashboards-distribution-header-toggle">
															<span class="dashboards-distribution-header-current">
																<?php esc_html_e( 'All', 'sts' ) ?>
															</span>
                                                            <span class="dashicons dashicons-arrow-down"></span>
                                                        </a>
                                                        <div class="dropdown-menu dashboard-ticket-dropdown">
                                                            <a href="#" class="dropdown-item"
                                                               data-sts-ladda="true"
                                                               data-sts-action="sts_dashboards_filter_ticket"
                                                               data-sts-action-param="<?php echo esc_attr( json_encode( array(
																   'user'  => 'all',
																   'nonce' => wp_create_nonce( 'sts_dashboards_get_ticket_security' )
															   ) ) ) ?>"
                                                               data-sts-callback="STS.dashboardsUpdateFilter">
																<?php esc_html_e( 'All', 'sts' ) ?>
                                                            </a>
                                                            <a href="#" class="dropdown-item"
                                                               data-sts-ladda="true"
                                                               data-sts-action="sts_dashboards_filter_ticket"
                                                               data-sts-action-param="<?php echo esc_attr( json_encode( array(
																   'user'  => 'my-ticket',
																   'nonce' => wp_create_nonce( 'sts_dashboards_get_ticket_security' )
															   ) ) ) ?>"
                                                               data-sts-callback="STS.dashboardsUpdateFilter">
																<?php esc_html_e( 'My tickets', 'sts' ) ?>
                                                            </a>
                                                        </div>
                                                    </div>
												<?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="sts-widget-content dashboards-distribution-content">
                                            <div class="dashboards-ticket">
												<?php
												if ( $tickets ):
													$contents = array();
													foreach ( $tickets as $ticket ) {
														$contents[] = array(
															'url'           => sts_link_ticket( $ticket->id ),
															'subject'       => $ticket->subject,
															'updating_date' => sts_time_ago( strtotime( sts_set_updating_date( $ticket->id ) ) )
														);
													}
													STS()->get_template( 'dashboards/ticket-item.php',
														array( 'tickets' => $contents ) );
												else: ?>
                                                    <span><?php esc_html_e( 'Current no have ticket!', 'sts' ) ?></span>
												<?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sts-sidebar">
                        <div class="sts-sidebar__item">
                            <div class="dashboard__time">
                                <div class="dashboard__time-header">
                                    <span class="dashboard__time-clock-day"></span>
                                    <span class="dashboard__time-clock-hours"></span>
                                    <span class="dashboard__time-clock-minute"></span>
                                    <span class="dashboard__time-clock-second"></span>
                                </div>
                                <div class="dashboard__time-body">
                                    <span class="dashboard__time-month"></span>
                                    <span class="dashboard__time-day"></span>
                                    <span class="dashboard__time-year"></span>
                                </div>
                            </div>
                        </div>
						<?php if ( ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) ||
						             in_array( 'administrator', $user_roles ) ) ):
							$notes = STS()->db()->notes()->get_notes_by_user( $current_user->ID, 0 );
							$nb_note = STS()->db()->notes()->count_notes_by_user( $current_user->ID );
							$limit = STS()->db()->notes()->limit_note;
							$numbPage = ceil( $nb_note / $limit );
							?>
                            <div class="sts-sidebar__item">
                                <div class="sts-widget">
                                    <div class="sts-widget-header">
										<?php esc_html_e( 'Your notes', 'sts' ) ?>
                                    </div>
                                    <div class="sts-widget-content">
                                        <div class="note-items">
											<?php if ( $notes ):
											$contents = array();
											foreach ( $notes as $note ):
												$contents[] = array(
													'url'          => sts_link_ticket( $note->ticket_id ),
													'message'      => strlen( $note->message ) > 50 ? sts_truncate_str( $note->message, 50 ) . '...' : $note->message,
													'created_date' => date( 'F d,Y', strtotime( $note->created_date ) )
												);
												?>
											<?php endforeach;
											STS()->get_template( 'dashboards/note-item-dashboard.php', array( 'notes' => $contents ) );
											?>
                                        </div>
										<?php
										if ( $nb_note > $limit ): ?>
                                            <form method="post" action="" id="form-paginator-listing-note"
                                                  class="form close"
                                                  data-sts-form-action="sts_note_listing_paginator"
                                                  data-sts-callback="STS.paginatorProcess">
                                                <input type="hidden" value="1" name="current_page" id="current-page"
                                                       class="current-page">
												<?php wp_nonce_field( 'sts_note_paginator_security', 'nonce' ); ?>
                                            </form>
                                            <div class="listing-note-paginator">
												<?php STS()->get_template( 'paginator.php', array(
													'numbPage'     => $numbPage,
													'offset'       => 0,
													'current_page' => 1,
													'target'       => '#form-paginator-listing-note',
													'limit'        => $limit,
													'total'        => $nb_note
												) );
												?>
                                            </div>
										<?php
										endif;
										else: ?>
                                            <div class="sts-no-content"><?php esc_html_e( 'You have no note!!', 'sts' ) ?></div>
										<?php endif; ?>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

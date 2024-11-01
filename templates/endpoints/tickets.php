<?php
$current_user   = wp_get_current_user();
$current_status = '';
if ( isset( $_GET['status'] ) ) {
	$status         = sanitize_text_field( $_GET['status'] );
	$current_status = $status;
	if ( $status == 'request' ) {
		$status = 1;
	} elseif ( $status == 'responded' ) {
		$status = 2;
	} elseif ( ( $status == 'close' ) ) {
		$status = 3;
	}
} else {
	$status = '';
}
if ( isset( $_GET['cat_id'] ) && $_GET['cat_id'] != '' ) {
	$theme_id = sanitize_text_field( $_GET['cat_id'] );
} elseif ( isset( $_GET['theme_id'] ) && $_GET['theme_id'] != '' ) {
	$theme_id = sanitize_text_field( $_GET['theme_id'] );
} else {
	$theme_id = '';
}

$user_meta       = get_userdata( $current_user->ID );
$user_roles      = $user_meta->roles;
if ( in_array( 'subscriber', $user_roles ) || in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ):
	$ticket_arr = apply_filters( 'sts_get_all_ticket', array(
		'status'  => $status,
		'offset'  => 0,
		'user_id' => $current_user->ID,
		'theme'   => $theme_id,
	) );
	$tickets     = $ticket_arr['tickets'];
	$numbTicket  = $ticket_arr['all_tickets'];
	$limit       = STS()->db()->tickets()->limit_ticket;
	$numbPage    = ceil( $numbTicket / $limit );
	$allTicketID = STS()->db()->tickets()->get_all_ticket_id();
	?>
    <div class="sts-page-main listing-ticket">
        <div class="sts-page-banner user-banner">
            <div class="container-fluid">
                <div class="sts-page-banner-content">
                    <h3 class="sts-page-title">
						<?php
						if ( $current_status == 'request' ) {
							esc_html_e( 'Request tickets', 'sts' );
						}
						if ( $current_status == 'responded' ) {
							esc_html_e( 'Responded tickets', 'sts' );
						}
						if ( $current_status == 'close' ) {
							esc_html_e( 'Closed tickets', 'sts' );
						}
						if ( $current_status == 'processing' ) {
							esc_html_e( 'Processing tickets', 'sts' );
						}
						if ( $current_status == 'following' ) {
							esc_html_e( 'Following tickets', 'sts' );
						}
						if ( $current_status == 'my-ticket-all' ) {
							esc_html_e( 'My all tickets', 'sts' );
						}
						if ( $current_status == '' ) {
							esc_html_e( "All tickets ", "sts" );
						}
						?>
                    </h3>
                </div>
                <div class="form__message"></div>
            </div>
        </div>
        <div class="sts-page-container">
            <div class="container-fluid">
                <div class="sts-page-content-main">
                    <div class="listing-ticket__container">
                        <div class="listing-ticket__filter">
                            <form method="post" action="" id="form-listing-ticket"
                                  data-sts-form-action="sts_load_data_ticket_init" data-sts-ladda="true">
								<?php wp_nonce_field( 'processing-listing-ticket', 'sts_listing_ticket_filter_nonce_field' ); ?>
                                <div class="listing-ticket__filter-wrapper">
                                    <input type="hidden" name="current_page" id="current-page"
                                           value="<?php esc_attr_e( '1', 'sts' ) ?>"
                                           class="current-page">
                                    <input type="hidden" name="current_status" id="current_status"
                                           value="<?php echo esc_attr( $status ) ?>">
									<?php
									do_action( 'sts_tickets_filter_first' );
									?>
                                    <div class="listing-ticket__search">
                                        <div class="form-control-group">
                                            <label for="search" class="form-control-label">
												<?php esc_html_e( "Search", "sts" ) ?>
                                            </label>
                                            <input id="search" class="form-control" name="keyWord" type="text"
                                                   placeholder="<?php esc_attr_e( 'Search by subject of ticket', 'sts' ) ?>">
                                            <div class="form-control-group-icon">
                                                <span class="dashicons dashicons-search"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="listing-ticket__filter-theme">
                                        <div class="form-control-group">
											<?php
											do_action( 'sts_tickets_filter_second' );
											?>
                                            <select class="form-control" id="theme" name="theme">
                                                <option value=""><?php esc_html_e( 'All', 'sts' ) ?></option>
												<?php
												$themes = STS()->db()->themes()->getting_theme_all();
												if ( $themes ):
													foreach ( $themes as $theme ):
														?>
                                                        <option value="<?php echo esc_attr( $theme->theme_id ); ?>" <?php selected( $theme_id, $theme->theme_id ) ?>><?php echo esc_html( $theme->theme_name ); ?></option>
													<?php endforeach;
												endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="listing-ticket__filter-status">
                                        <div class="form-control-group">
                                            <label class="form-control-label" for="ftstatus">
												<?php esc_html_e( "Status:", "sts" ) ?>
                                            </label>
                                            <select class="form-control" id="ftstatus" name="ftstatus">
                                                <option value=""><?php esc_html_e( 'All', 'sts' ) ?></option>
                                                <option <?php selected( $status, '1' ) ?> value="1">
													<?php esc_html_e( 'Request', 'sts' ) ?>
                                                </option>
                                                <option <?php selected( $status, '2' ) ?> value="2">
													<?php esc_html_e( 'Responded', 'sts' ) ?>
                                                </option>
                                                <option <?php selected( $status, '3' ) ?> value="3">
													<?php esc_html_e( 'Close', 'sts' ) ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="listing-ticket__filter-button">
                                        <button type="submit" class="btn btn-primary">
											<?php esc_html_e( 'Search', 'sts' ) ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="listing-ticket__initing"
							<?php if ( ( in_array( 'supporter', $user_roles ) ||
							             in_array( 'leader_supporter', $user_roles ) ||
							             in_array( 'administrator', $user_roles ) ) && count( $allTicketID ) > 0 ): ?>
                                data-ticket-id="<?php echo esc_attr( implode( ",", $allTicketID ) ) ?>"
                                data-nonce="<?php echo esc_attr( wp_create_nonce( 'sts_auto_sending_security' ) ) ?>"
							<?php endif; ?>>
							<?php if ( $tickets ):
								?>
                                <div class="listing-ticket__items">
									<?php $contents = sts_set_ticket( $tickets );
									STS()->get_template( 'tickets/ticket-item.php', array( 'contents' => $contents ) );
									?>
                                </div>
								<?php if ( $numbTicket > $limit ): ?>
								<?php STS()->get_template( 'paginator.php', array(
									'numbPage'     => $numbPage,
									'offset'       => 0,
									'current_page' => 1,
									'target'       => '#form-listing-ticket',
									'limit'        => $limit,
									'total'        => $numbTicket
								) ) ?>
							<?php endif; ?>
							<?php else: ?>
                                <div class="sts-no-content"><?php esc_html_e( 'No ticket found!!', 'sts' ) ?></div>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
endif;
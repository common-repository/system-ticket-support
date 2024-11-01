<?php
$current_user = wp_get_current_user();
$status       = '';
if ( isset( $_GET['status'] ) && $_GET['status'] != '' ) {
	$status = sanitize_text_field( $_GET['status'] );
}
$supporter_id = sanitize_text_field( $_GET['supporter_id'] );
$user_meta    = get_userdata( $current_user->ID );
$user_roles   = $user_meta->roles;
$supporter    = get_user_by( 'ID', $supporter_id );

if ( $status == '' ) {
	$ticketNum = STS()->db()->tickets()->count_ticket_by_supporter( $supporter_id );
} else {
	if ( $status == 'open' ) {
		$ticketNum = STS()->db()->tickets()->count_ticket_by_supporter_by_status_open( $supporter_id );
	} else {
		$ticketNum = STS()->db()->tickets()->count_ticket_by_supporter_by_status_close( $supporter_id );
	}
}

$tickets  = STS()->db()->tickets()->get_ticket_by_supporter( $supporter_id, $status, 0 );
$limit    = STS()->db()->tickets()->limit_ticket;
$numbPage = ceil( $ticketNum / $limit );

?>
<div class="sts-page-main">
    <div class="sts-page-banner user-banner">
        <div class="container-fluid">
            <div class="sts-user">
				<?php STS()->get_template( 'users/user-banner.php', array(
					'avatar_url' => sts_get_avatar( $supporter_id, 40, 56 ),
					'name'       => $supporter->display_name,
					'email'      => $supporter->user_email
				) ) ?>
            </div>
            <div class="form__message"></div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
			<?php if ( in_array( 'administrator', $user_roles ) ): ?>
                <div class="sts-page-content-main">
                    <div class="sts-page-profile-header">
                        <span class="sts-page-profile-header-item"><?php esc_html_e( 'All tickets', 'sts' ) ?></span>

                    </div>
                    <div class="supporter-ticket">
                        <form method="post" action="" id="supporter-ticket-filter"
                              data-sts-form-action="sts_supporter_details_filter" data-sts-ladda="true">
							<?php wp_nonce_field( 'sts_filter_ticket_nonce_field', 'nonce' ); ?>
                            <div class="supporter-ticket__filter">
                                <div class="supporter-ticket__filter-wrapper">
                                    <input type="hidden" name="supporterID"
                                           value="<?php echo esc_attr( $supporter_id ); ?>">
                                    <input type="hidden" name="current_page" id="current-page"
                                           class="current-page"
                                           value="<?php esc_attr_e( '1', 'sts' ) ?>">
                                    <input type="hidden" name="current_status"
                                           value="<?php echo esc_attr( $status ) ?>">
                                    <div class="form-control-group form-search">
                                        <label class="form-control-label" for="search">
											<?php esc_html_e( 'Search', 'sts' ) ?>
                                        </label>
                                        <input id="search" class="form-control" type="text" name="keyWord"
                                               placeholder="<?php esc_attr_e( 'Search by subject of tickets', 'sts' ) ?>">
                                    </div>
                                    <div class="form-control-group form-status">
                                        <label class="form-control-label" for="status">
											<?php esc_html_e( 'Status:', 'sts' ) ?>
                                        </label>
                                        <select class="form-control" id="ftstatus" name="ftstatus">
                                            <option value=""><?php esc_html_e( 'All', 'sts' ) ?></option>
                                            <option value="<?php esc_attr_e( '2', 'sts' ) ?>" <?php selected( $status, 'open' ) ?>><?php esc_html_e( 'Responded', 'sts' ) ?></option>
                                            <option value="<?php esc_attr_e( '3', 'sts' ) ?>" <?php selected( $status, 'close' ) ?>><?php esc_html_e( 'Close', 'sts' ) ?></option>
                                        </select>
                                    </div>
                                    <div class="form-control-group">
                                        <label class="form-control-label" for="from-date">
											<?php esc_html_e( 'Form date', 'sts' ) ?>
                                        </label>
                                        <input class="form-control sts-datepicker" type="text" id="from-date"
                                               name="fromDate">
                                    </div>
                                    <div class="form-control-group">
                                        <label class="form-control-label" for="to-date">
											<?php esc_html_e( 'To date', 'sts' ) ?>
                                        </label>
                                        <input class="form-control sts-datepicker" type="text" id="to-date"
                                               name="toDate">
                                    </div>
                                    <div class="form-button-search">
                                        <button type="submit" id="button-search" class="btn btn-primary">
											<?php esc_html_e( 'Search', 'sts' ) ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="listing-ticket listing-ticket__initing supporter-ticket__init">
							<?php if ( $tickets ):
								$contents = sts_set_ticket( $tickets );
								STS()->get_template( 'tickets/ticket-item.php', array( 'contents' => $contents ) );
								?>
								<?php if ( $ticketNum > $limit ): ?>
								<?php STS()->get_template( 'paginator.php', array(
									'numbPage'     => $numbPage,
									'offset'       => 0,
									'current_page' => 1,
									'target'       => '#supporter-ticket-filter',
									'limit'        => $limit,
									'total'        => $ticketNum
								) ) ?>
							<?php endif; ?>
							<?php else: ?>
                                <div class="sts-no-content"><?php esc_html_e( 'No ticket found!!', 'sts' ) ?></div>
							<?php endif; ?>
                        </div>
                    </div>

                </div>
			<?php elseif ( in_array( 'subscriber', $user_roles ) || in_array( 'supporter', $user_roles )
			               || in_array( 'leader_supporter', $user_roles ) ):
				$user_signature = get_user_meta( $supporter->ID, 'sts_user_signature', true );
				STS()->get_template( 'supporters/supporter-details.php',
					array(
						'supporter_name' => $supporter->display_name,
						'supporter_mail' => $supporter->user_email,
						'avatar_url'     => sts_get_avatar( $supporter_id, 100, 100 ),
						'signature'      => $user_signature
					) );
				?>
			<?php endif; ?>
        </div>
    </div>
</div>


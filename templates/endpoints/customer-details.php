<?php
$current_user = wp_get_current_user();
$customer_id  = sanitize_text_field( $_GET['customer_id'] );
$user_meta    = get_userdata( $current_user->ID );
$user_roles   = $user_meta->roles;
$customer     = get_user_by( 'ID', $customer_id );
$ticketNumb   = count( STS()->db()->tickets()->get_ticket_by_customer_all( $customer_id ) );
$ticket_arr   = STS()->db()->tickets()->get_tickets_by_customer( '', $customer_id, 0 );
$tickets      = $ticket_arr['tickets'];
$limit        = STS()->db()->tickets()->limit_ticket;
$numbPage     = ceil( $ticketNumb / $limit );
$allTicketID  = STS()->db()->tickets()->get_ticket_id_by_customer( $customer_id );
$is_lock      = get_user_meta( $customer_id, 'sts_is_lock', true );
?>
<div class="sts-page-main customer-details">
    <div class="sts-page-banner user-banner">
        <div class="container-fluid">
            <div class="sts-user">
				<?php STS()->get_template( 'users/user-banner.php', array(
					'avatar_url' => sts_get_avatar( $customer_id, 40, 56 ),
					'name'       => $customer->display_name,
					'email'      => $customer->user_email
				) ) ?>
                <div class="customer__action">
					<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles )
					           || in_array( 'administrator', $user_roles ) ) : ?>
                        <div id="sts-lock-user-<?php echo esc_attr( $customer_id ) ?>"
                             class="action">
							<?php if ( $is_lock == 0 ): ?>
                                <a class="delete sts-delete-customer"
                                   id="customer-delete" href="#"
                                   data-sts-ladda="true"
                                   data-sts-action="sts_delete_customer"
                                   data-sts-action-param="<?php echo esc_attr( json_encode( array(
									   'id'    => $customer_id,
									   'nonce' => wp_create_nonce( 'sts_frontend_delete_customer' )
								   ) ) ) ?>"
                                   data-sts-confirm="<?php esc_attr_e( 'Are you sure delete this user?', 'sts' ) ?>"

                                   title="<?php esc_attr_e( 'Delete', 'sts' ) ?>">
                                    <span class="dashicons dashicons-trash"></span>
                                </a>
							<?php else: ?>
                                <a href="#" class="edit"
                                   data-sts-ladda="true"
                                   data-sts-action="sts_unlock_user"
                                   data-sts-action-param="<?php echo esc_attr( json_encode( array(
									   'id'    => $customer_id,
									   'nonce' => wp_create_nonce( 'sts_frontend_unlock_user' )
								   ) ) ) ?>"
                                   data-sts-confirm="<?php esc_attr_e( 'Are you sure unlock this user?', 'sts' ) ?>"
                                   title="<?php esc_html_e( 'Unlock', 'sts' ) ?>">
                                    <span class="dashicons dashicons-unlock"></span>
                                </a>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                    <a href="<?php echo esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $customer->ID . '&page=customer-details' ) ) ?>"
                       class="customer__action-item"
                       title="<?php esc_attr_e( 'Edit', 'sts' ) ?>">
                        <span class="dashicons dashicons-edit"></span>
                    </a>
                </div>
            </div>
            <div class="form__message"></div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
            <div class="customer__addition-info">
                <div id="custom_carousel" class="sts-page-content-main carousel slide" data-ride="carousel">
                    <div class="controls sts-page-profile-header">
                        <ul class="carousel-indicators">
                            <li data-target="#custom_carousel" data-slide-to="0"
                                class="active tab-item sts-page-profile-header-item" tabindex="1">
                                <a href="#"><span class="dashicons dashicons-tag"></span>
									<?php printf( esc_html__( '%s tickets', 'sts' ), $ticketNumb ) ?>
                                </a>
                            </li>
							<?php
							do_action( 'sts_customer_tab_item' )
							?>
                        </ul>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="customer-ticket">
                                <div class="listing-ticket listing-ticket__initing customer-ticket__items"
									<?php if ( ( in_array( 'supporter', $user_roles ) ||
									             in_array( 'leader_supporter', $user_roles ) ||
									             in_array( 'administrator', $user_roles ) ) && count( $allTicketID ) > 0 ): ?>
                                        data-ticket-id="<?php echo esc_attr( implode( ',', $allTicketID ) ) ?>"
                                        data-nonce="<?php echo esc_attr( wp_create_nonce( 'sts_auto_sending_security' ) ) ?>"
									<?php endif; ?>>
									<?php if ( $tickets ):
										$contents = sts_set_ticket( $tickets );
										STS()->get_template( 'tickets/ticket-item.php', array( 'contents' => $contents ) );
									else: ?>
                                        <div class="sts-no-content">
											<?php esc_html_e( 'No ticket found!!', 'sts' ) ?>
                                        </div>
									<?php endif; ?>
                                </div>
								<?php if ( $ticketNumb > $limit ): ?>
                                    <form method="post" action=""
                                          id="form-paginator-customer-ticket"
                                          class="close"
                                          data-sts-form-action="sts_customer_details_paginator"
                                          data-sts-callback="STS.paginatorProcess">
                                        <input type="hidden" value="1" name="current_page"
                                               class="current-page">
                                        <input type="hidden"
                                               value="<?php echo esc_attr( $customer->ID ) ?>"
                                               name="customer_id">
										<?php wp_nonce_field( 'sts_customer_paginator_ticket_security', 'nonce' ); ?>
                                    </form>
                                    <div class="customer-ticket-paginator">
										<?php STS()->get_template( 'paginator.php', array(
											'numbPage'     => $numbPage,
											'offset'       => 0,
											'current_page' => 1,
											'target'       => '#form-paginator-customer-ticket',
											'limit'        => $limit,
											'total'        => $ticketNumb
										) ) ?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
						<?php
						do_action( 'sts_customer_tab_before' );
						do_action( 'sts_customer_tab_middle', $customer_id );
						do_action( 'sts_customer_tab_after', $customer_id );
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

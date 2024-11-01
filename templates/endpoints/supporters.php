<?php
$supporters = get_users( array( 'role__in' => array( 'administrator', 'supporter', 'leader_supporter' ) ) );
?>
<div class="sts-page-main listing-customer">
    <div class="sts-page-banner user-banner">
        <div class="container-fluid">
            <div class="sts-page-banner-content banner-center">
                <h3 class="sts-page-title">
					<?php
					esc_html_e( 'Manage supporters', 'sts' )
					?>
                </h3>
            </div>
        </div>
    </div>
    <div class="sts-page-container">
        <div class="container-fluid">
            <div class="sts-page-content-main">
                <table class="table supporter-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'ID', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Name', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Email', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Close tickets', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Open ticket', 'sts' ) ?></th>
                            <th><?php esc_html_e( 'Action', 'sts' ) ?></th>
                        </tr>
                    </thead>
                    <tbody>
						<?php
						if ( $supporters ):
							foreach ( $supporters as $supporter ):
								$openTickets = STS()->db()->tickets()->count_ticket_by_supporter_by_status_open( $supporter->ID );
								$closeTickets = STS()->db()->tickets()->count_ticket_by_supporter_by_status_close( $supporter->ID );
								$is_lock = get_user_meta( $supporter->ID, 'sts_is_lock', true );
								?>
                                <tr id="tr-<?php echo esc_attr( $supporter->ID ) ?>">
                                    <td
                                            data-mobile-label="<?php esc_attr_e( 'ID', 'sts' ) ?>">
										<?php echo esc_html( $supporter->ID ) ?>
                                    </td>
                                    <td data-mobile-label="<?php esc_attr_e( 'Name', 'sts' ) ?>" width="30%">
                                        <a class="table__link"
                                           href="<?php echo esc_url( sts_support_page_url( 'supporter-details/?supporter_id=' . $supporter->ID ) ); ?>">
                                            <img src="<?php echo esc_url( sts_get_avatar( $supporter->ID, 100, 100 ) ) ?>"
                                                 class="customer-avatar"
                                                 alt="<?php esc_attr_e( 'Avatar', 'sts' ) ?>">
                                            <span><?php echo esc_html( $supporter->display_name ) ?></span>
                                        </a>
                                    </td>
                                    <td data-mobile-label="<?php esc_attr_e( 'Email', 'sts' ) ?>" width="25%">
										<?php echo esc_html( $supporter->user_email ) ?>
                                        <div class="addition-info">
                                            <div class="addition-info__item">
                                                <span class="addition-info__label"><?php esc_html_e( 'Register:', 'sts' ) ?></span>
                                                <span class="addition-info__value"><?php echo esc_html( $supporter->user_registered ) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-mobile-label="<?php esc_attr_e( 'Close tickets', 'sts' ) ?>">
                                        <a href="<?php echo esc_url( sts_support_page_url( 'supporter-details/?supporter_id=' . $supporter->ID . '&status=close' ) ); ?>">
											<?php echo esc_html( $closeTickets ); ?>
                                        </a>
                                    </td>
                                    <td data-mobile-label="<?php esc_attr_e( 'Open tickets', 'sts' ) ?>">
                                        <a
                                                href="<?php echo esc_url( sts_support_page_url( 'supporter-details/?supporter_id=' . $supporter->ID . '&status=open' ) ); ?>">
											<?php echo esc_html( $openTickets ); ?>
                                        </a>
                                    </td>
                                    <td data-mobile-label="<?php esc_attr_e( 'Action', 'sts' ) ?>" class="table-action">
                                        <div class="action-items">
                                            <a class="action edit"
                                               title="<?php esc_attr_e( 'View details', 'sts' ) ?>"
                                               href="<?php echo esc_url( sts_support_page_url( 'supporter-details/?supporter_id=' . $supporter->ID ) ); ?>   ">
                                                <span class="dashicons dashicons-info"></span>
                                            </a>
                                            <a class="action edit"
                                               title="<?php esc_attr_e( 'Edit', 'sts' ) ?>"
                                               href="<?php echo esc_url( sts_support_page_url( 'user-profile/?profile_id=' . $supporter->ID . '&page=supporters' ) ) ?>">
                                                <span class="dashicons dashicons-edit"></span>
                                            </a>
                                            <div id="sts-lock-user-<?php echo esc_attr( $supporter->ID ) ?>"
                                                 class="action">
												<?php if ( $is_lock == 0 ): ?>
                                                    <a href="#" class="delete sts-delete-supporter"
                                                       data-sts-ladda="true"
                                                       data-sts-action="sts_delete_supporter"
                                                       data-sts-action-param="<?php echo esc_attr( json_encode( array(
														   'id'    => $supporter->ID,
														   'nonce' => wp_create_nonce( 'sts_frontend_delete_supporter' )
													   ) ) ) ?>"
                                                       data-sts-confirm="<?php esc_attr_e( 'Are you sure delete this user?', 'sts' ) ?>"
                                                       title="<?php esc_html_e( 'Delete', 'sts' ) ?>">
                                                        <span class="dashicons dashicons-trash"></span>
                                                    </a>
												<?php else: ?>
                                                    <a href="#"
                                                       data-sts-ladda="true"
                                                       data-sts-action="sts_unlock_user"
                                                       data-sts-action-param="<?php echo esc_attr( json_encode( array(
														   'id'    => $supporter->ID,
														   'nonce' => wp_create_nonce( 'sts_frontend_unlock_user' )
													   ) ) ) ?>"
                                                       data-sts-confirm="<?php esc_attr_e( 'Are you sure unlock this user?', 'sts' ) ?>"
                                                       title="<?php esc_html_e( 'Unlock', 'sts' ) ?>">
                                                        <span class="dashicons dashicons-unlock"></span>
                                                    </a>
												<?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
							<?php
							endforeach;
						else:?>
                            <tr>
                                <td colspan="100%"><?php esc_html_e( 'No supporter found!!', 'sts' ) ?> </td>
                            </tr>
						<?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
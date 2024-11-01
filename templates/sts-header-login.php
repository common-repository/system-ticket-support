<?php
$current_user           = wp_get_current_user();
if ( $current_user->exists() ):
	$notifications = STS()->db()->notification()->get_notification_by_user( $current_user->ID, 0 );
	$nb_notification    = STS()->db()->notification()->count_notification_by_user( $current_user->ID );
	$notification_count = STS()->db()->notification()->count_notification_not_read( $current_user->ID );
	$user_meta          = get_userdata( $current_user->ID );
	$user_roles         = $user_meta->roles;
	$limit              = STS()->db()->notification()->limit_notification;
	$numbPage           = ceil( ( $nb_notification ) / $limit );
	$avatar             = sts_get_avatar( $current_user->ID, 80, 80 )
	?>
    <div class="top-bar login">
        <div class="container-fluid">
            <div class="top-bar__container">
                <div class="top-bar__left">
                    <a href="#" class="top-bar__toggle"><span class="dashicons dashicons-menu"></span></a>
                </div>
				<?php $is_first_login = get_user_meta( $current_user->ID, 'sts_first_login', true );
				?>
                <div class="top-bar__page-header">
					<?php $is_first_login ? printf( esc_html__( 'Well come, %s!', 'sts' ), $current_user->display_name ) : printf( esc_html__( 'Well come back, %s!', 'sts' ), $current_user->display_name ) ?>
                </div>
                <div class="top-bar__right">
                    <ul class="top-bar__right-menu">
						<?php if ( in_array( 'subscriber', $user_roles ) || in_array( 'administrator', $user_roles ) ): ?>
                            <li class="top-bar__right-menu-item top-bar__right-menu-item--ticket">
                                <a href="<?php echo esc_url( sts_support_page_url( 'submit-ticket' ) ) ?>"
                                   class="top-bar__right-menu-link"><span class="dashicons dashicons-tag"></span><span><?php esc_html_e( "Submit a ticket", "sts" ) ?></span></a>
                            </li>
						<?php endif; ?>
                        <li class="top-bar__right-menu-item top-bar__right-menu-item--notification">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                   class="top-bar__right-menu-link" id="notification-link"
									<?php if ( $notification_count > 0 ): ?>
                                        data-sts-action="sts_process_notification"
                                        data-sts-action-param="<?php echo esc_attr( json_encode( array(
											'nonce' => wp_create_nonce( 'sts_notification_security' )
										) ) ) ?>"
                                        data-sts-callback="STS.showClosedContent"
									<?php endif; ?>>
                                    <span class="dashicons dashicons-megaphone"></span>
                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
									<?php if ( $notification_count > 0 ): ?>
                                        <span
                                                class="notification-number"><?php echo esc_html( $notification_count ); ?></span>
									<?php endif; ?>
                                </a>

                                <div class="dropdown-menu dropdown-menu-notification">
                                    <div class="notification">
                                        <div class="notification__title">
                                            <span><?php esc_html_e( 'Notification', 'sts' ) ?></span>
                                        </div>
                                        <div class="notification__container"
											<?php if ( $limit < $nb_notification ):
												?>
                                                data-sts-scroll-action="sts_get_notification"
                                                data-sts-action-param="<?php echo esc_attr( json_encode( array(
													'nonce' => wp_create_nonce( 'sts_get_notification_security' ),
												) ) ) ?>"
                                                data-sts-callback="STS.setCurrentPage"
											<?php endif; ?>>
                                            <input type="hidden" name="current_page" id="notification-current-page"
                                                   value="<?php esc_attr_e( '2', 'sts' ) ?>">
                                            <input type="hidden" name="total_page" id="total-page"
                                                   value="<?php echo esc_attr( $numbPage ) ?>">
                                            <div class="form__message" id="notification-message"></div>
											<?php if ( $notifications ):
												$contents = array();
												foreach ( $notifications as $notification ) {
													$contents[] = array(
														'link'         => $notification->link,
														'content'      => $notification->content,
														'created_date' => $notification->created_date
													);
												}
												STS()->get_template( 'notifications/notification-item.php',
													array(
														'notifications' => $contents
													) );
											endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="top-bar__right-menu-item top-bar__right-menu-item--user">
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                   class="top-bar__right-menu-link menu-user">
                                    <img class="menu-user__image" alt="avatar" width="50" height="50"
                                         src="<?php echo esc_url( $avatar ) ?>">
                                    <span
                                            class="top-bar__right-menu-label menu-user__name"><?php echo esc_html( $current_user->display_name ); ?></span>
                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                                </a>

                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                       href="<?php echo esc_url( sts_support_page_url( 'user-profile/?page=my-profile' ) ) ?>"><?php esc_html_e( "My profile", "sts" ) ?></a>
                                    <a class="dropdown-item"
                                       href="<?php echo esc_url( wp_logout_url( sts_support_page_url( '/login' ) ) ) ?>"><?php esc_html_e( "Sign out", "sts" ) ?></a>
									<?php if ( in_array( 'administrator', $user_roles ) ): ?>
                                        <a class="dropdown-item"
                                           href="<?php echo esc_url( admin_url() ) ?>"><?php esc_html_e( "Go to admin", "sts" ) ?></a>
									<?php endif; ?>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
<?php endif; ?>
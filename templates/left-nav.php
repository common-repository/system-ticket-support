<?php
$current_user = wp_get_current_user();
$user_meta    = get_userdata( $current_user->ID );
$user_roles   = $user_meta->roles;
if ( in_array( 'subscriber', $user_roles ) ) {
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
}
$numbTicket_all       = $all_tickets['all_tickets'];
$numbTicket_request   = $ticket_arr_request['all_tickets'];
$numbTicket_responded = $ticket_arr_responded['all_tickets'];
$numbTicket_close     = $ticket_arr_close['all_tickets'];

?>
<div class="left-sidebar">
    <div class="left-sidebar__logo">
        <div class="left-sidebar__logo-container">
            <div class="left-sidebar__logo-wrapper">
                <div class="page-logo">
					<?php the_custom_logo() ?>
                </div>
                <a class="page-toggle" tabindex="1"><span class="dashicons dashicons-menu"></span>
                </a>
                <div class="page-close"><span class="dashicons dashicons-arrow-left-alt"></span></div>
            </div>
        </div>
    </div>
    <div class="left-sidebar__main">
        <div class="left-sidebar__container">
            <div class="left-sidebar__divider"></div>
            <div class="left-sidebar__item">
                <div class="left-sidebar__category">
                    <span><?php esc_html_e( 'Application', 'sts' ) ?></span>
                </div>
                <ul class="main-menu">
                    <li class="main-menu__item <?php if ( STS()->endpoints()->is_current_endpoint( 'dashboards' ) || STS()->endpoints()->get_current_endpoint() == '' ) {
						echo 'active';
					} ?>">
                        <a href="<?php echo esc_url( sts_support_page_url( 'dashboards' ) ) ?>">
                            <span class="main-menu__icon"><span class="dashicons dashicons-dashboard"></span></span>
                            <span class="main-menu__name"><?php esc_html_e( 'Dashboards', 'sts' ) ?></span>
                        </a>
                    </li>
                    <li class="main-menu__item multi-menu">
                        <a class="multi-menu__toggle" data-toggle="collapse" data-target="#subMenu">
                            <span class="main-menu__icon"><span class="dashicons dashicons-tag"></span></span>

                            <div class="main-menu__name"><span><?php esc_html_e( 'Tickets', 'sts' ) ?></span>
                                <span class="icon arrow-right <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) ||
								                                         STS()->endpoints()->is_current_endpoint( 'ticket-details' )
								                                         || STS()->endpoints()->is_current_endpoint( 'dashboards' ) || STS()->endpoints()->get_current_endpoint() == '' ) {
									echo 'open';
								} ?>"><span class="dashicons dashicons-arrow-right-alt2"></span></span>
                            </div>
                        </a>
                        <ul class="multi-menu__sub <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' )
						                                      || STS()->endpoints()->is_current_endpoint( 'ticket-details' ) ||
						                                      STS()->endpoints()->is_current_endpoint( 'dashboards' ) || STS()->endpoints()->get_current_endpoint() == '' ) {
							echo 'show';
						} else {
							echo 'collapse';
						}
						?>" id="subMenu">
                            <li class="main-menu__item <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) && isset( $_GET['status'] ) && $_GET['status'] == 'request' ) {
								echo 'active';
							} ?>">
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=request' ) ); ?>"
                                   data-url="/tickets/?status=request">
                                        <span
                                                class="main-menu__name"><?php esc_html_e( 'Tickets Request', 'sts' ) ?></span>
                                    <span
                                            class="icon icon-request"><?php echo esc_html( $numbTicket_request ) ?></span>
                                </a>
                            </li>
                            <li class="main-menu__item <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) && ! isset( $_GET['status'] ) ) {
								echo 'active';
							} ?>">
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets/' ) ); ?>"
                                >
                                    <span class="main-menu__name"><?php esc_html_e( 'All Tickets', 'sts' ) ?></span>
                                    <span
                                            class="icon icon-all"><?php echo esc_html( $numbTicket_all ) ?></span>
                                </a>
                            </li>
                            <li class="main-menu__item <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) && isset( $_GET['status'] ) && $_GET['status'] == 'responded' ) {
								echo 'active';
							} ?>">
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=responded' ) ); ?>"
                                >
                                <span
                                        class="main-menu__name"><?php esc_html_e( 'Tickets Responded', 'sts' ) ?></span>
                                    <span
                                            class="icon icon-responded"><?php echo esc_html( $numbTicket_responded ) ?></span>
                                </a>
                            </li>
							<?php if ( in_array( 'supporter', $user_roles ) ||
							           in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ):
								$ticket_arr_my = STS()->db()->tickets()->get_all_tickets( 'my-ticket-all', 0, $current_user->ID );
								$ticket_arr_pending = STS()->db()->tickets()->get_all_tickets( 'processing', 0, $current_user->ID );
								$ticket_arr_follow = STS()->db()->tickets()->get_all_tickets( 'following', 0, $current_user->ID );
								$numbTicket_my = $ticket_arr_my['all_tickets'];
								$numbTicket_pending = $ticket_arr_pending['all_tickets'];
								$numbTicket_follow = $ticket_arr_follow['all_tickets'];
								?>
                                <li class="main-menu__item <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) && isset( $_GET['status'] ) && $_GET['status'] == 'processing' ) {
									echo 'active';
								} ?>">
                                    <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=processing' ) ); ?>"
                                       data-url="/tickets/?status=pending">
                                <span
                                        class="main-menu__name"><?php esc_html_e( 'Tickets processing', 'sts' ) ?></span>
                                        <span
                                                class="icon icon-default"><?php echo esc_html( $numbTicket_pending ) ?></span>
                                    </a>
                                </li>
                                <li class="main-menu__item <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) && isset( $_GET['status'] ) && $_GET['status'] == 'following' ) {
									echo 'active';
								} ?>">
                                    <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=following' ) ); ?>"
                                       data-url="/tickets/?status=following">
                                <span
                                        class="main-menu__name"><?php esc_html_e( 'My Tickets Following', 'sts' ) ?></span>
                                        <span
                                                class="icon icon-default"><?php echo esc_html( $numbTicket_follow ) ?></span>
                                    </a>
                                </li>
							<?php endif; ?>
							<?php if ( in_array( 'supporter', $user_roles ) ||
							           in_array( 'leader_supporter', $user_roles ) || in_array( 'administrator', $user_roles ) ): ?>
                                <li class="main-menu__item <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) && isset( $_GET['status'] ) && $_GET['status'] == 'my-ticket-all' ) {
									echo 'active';
								} ?>">
                                    <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=my-ticket-all' ) ); ?>"
                                    >
                                <span
                                        class="main-menu__name"><?php esc_html_e( 'My All Tickets', 'sts' ) ?></span>
                                        <span
                                                class="icon icon-default"><?php echo esc_html( $numbTicket_my ) ?></span>
                                    </a>
                                </li>
							<?php endif; ?>
                            <li class="main-menu__item  <?php if ( STS()->endpoints()->is_current_endpoint( 'tickets' ) && isset( $_GET['status'] ) && $_GET['status'] == 'close' ) {
								echo 'active';
							} ?>">
                                <a href="<?php echo esc_url( sts_support_page_url( 'tickets/?status=close' ) ); ?>"
                                   data-url="/tickets/?status=close">
                                        <span
                                                class="main-menu__name"><?php esc_html_e( 'Tickets Closed', 'sts' ) ?></span>
                                    <span
                                            class="icon icon-close"><?php echo esc_html( $numbTicket_close ) ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
			<?php if ( in_array( 'supporter', $user_roles ) || in_array( 'leader_supporter', $user_roles ) ): ?>

                <div class="left-sidebar__item">
                    <div class="left-sidebar__divider"></div>
                    <div class="left-sidebar__category">
                        <span><?php esc_html_e( 'Management', 'sts' ) ?></span>
                    </div>
                    <ul class="main-menu">
                        <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'customers' ) ? 'active' : '' ?>">
                            <a href="<?php echo esc_url( sts_support_page_url( 'customers' ) ); ?>">
                                <span class="main-menu__icon"><span class="dashicons dashicons-admin-users"></span></span>
                                <span class="main-menu__name"><?php esc_html_e( 'Customers', 'sts' ) ?></span>
                            </a>
                        </li>
                        <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'templates' ) ? 'active' : '' ?>">
                            <a href="<?php echo esc_url( sts_support_page_url( 'templates' ) ); ?>"
                               data-url="/templates/">
                                <span class="main-menu__icon"><span class="dashicons dashicons-media-code"></span></span>
                                <span
                                        class="main-menu__name"><?php esc_html_e( 'Ticket Templates', 'sts' ) ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
			<?php elseif ( in_array( 'administrator', $user_roles ) ): ?>
                <div class="left-sidebar__item">
                    <div class="left-sidebar__divider"></div>
                    <div class="left-sidebar__category">
                        <span><?php esc_html_e( 'Management', 'sts' ) ?></span>
                    </div>
                    <ul class="main-menu">
                        <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'customers' ) ? 'active' : '' ?>">
                            <a href="<?php echo esc_url( sts_support_page_url( 'customers' ) ); ?>"
                            >
                                <span class="main-menu__icon"><span class="dashicons dashicons-money"></span></span>
                                <span class="main-menu__name"><?php esc_html_e( 'Customers', 'sts' ) ?></span>
                            </a>
                        </li>
                        <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'supporters' ) ? 'active' : '' ?>">
                            <a href="<?php echo esc_url( sts_support_page_url( 'supporters' ) ); ?>"
                            >
                                <span class="main-menu__icon"><span class="dashicons dashicons-groups"></span></span>
                                <span
                                        class="main-menu__name"><?php esc_html_e( 'Supporters Management', 'sts' ) ?></span>
                            </a>
                        </li>
                        <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'templates' ) ? 'active' : '' ?>">
                            <a href="<?php echo esc_url( sts_support_page_url( 'templates' ) ); ?>"
                            >
                                <span class="main-menu__icon"><span class="dashicons dashicons-media-code"></span></span>
                                <span
                                        class="main-menu__name"><?php esc_html_e( 'Ticket Templates', 'sts' ) ?></span>
                            </a>
                        </li>
                        <?php
                        do_action('sts_menu_left_middle');
                        ?>
                        <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'mail-templates' ) ? 'active' : '' ?>">
                            <a href="<?php echo esc_url( sts_support_page_url( 'mail-templates' ) ); ?>">
                                <span class="main-menu__icon"><span class="dashicons dashicons-email"></span></span>
                                <span class="main-menu__name"><?php esc_html_e( 'Mail templates', 'sts' ) ?></span>
                            </a>
                        </li>
                    </ul>
                    <div class="left-sidebar__divider"></div>
                    <div class="left-sidebar__category">
                        <span><?php esc_html_e( 'Report', 'sts' ) ?></span>
                    </div>
                    <ul class="main-menu">
                        <li class="main-menu__item multi-menu">
                            <a class="multi-menu__toggle" data-toggle="collapse" data-target="#subMenuReport">
                                <span class="main-menu__icon"><span class="dashicons dashicons-chart-pie"></span></span>

                                <div class="main-menu__name"><span><?php esc_html_e( 'Report', 'sts' ) ?></span>
                                    <span class="icon arrow-right <?php if ( STS()->endpoints()->is_current_endpoint( 'rating-report' )
									                                         || STS()->endpoints()->is_current_endpoint( 'supporter-report' ) ) {
										echo 'open';
									} ?>"><span class="dashicons dashicons-arrow-right-alt2"></span></span>
                                </div>
                            </a>
                            <ul class="multi-menu__sub <?php if ( STS()->endpoints()->is_current_endpoint( 'rating-report' )
							                                      || STS()->endpoints()->is_current_endpoint( 'supporter-report' ) ) {
								echo 'show';
							} else {
								echo 'collapse';
							}
							?>" id="subMenuReport">
                                <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'rating-report' ) ? 'active' : '' ?>">
                                    <a href="<?php echo esc_url( sts_support_page_url( 'rating-report' ) ) ?>">
                                        <span class="main-menu__name"><?php esc_html_e( 'Rating Report', 'sts' ) ?></span>
                                    </a>
                                </li>
                                <li class="main-menu__item <?php echo STS()->endpoints()->is_current_endpoint( 'supporter-report' ) ? 'active' : '' ?>">
                                    <a href="<?php echo esc_url( sts_support_page_url( 'supporter-report' ) ) ?>">
                                        <span class="main-menu__name"><?php esc_html_e( 'Supporter Report', 'sts' ) ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
			<?php endif; ?>
			<?php
			do_action('sts_menu_left_after');
			?>
        </div>
    </div>
</div>
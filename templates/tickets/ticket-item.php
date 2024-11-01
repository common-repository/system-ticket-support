<?php
foreach ( $contents as $content ):
	$current_user = wp_get_current_user();
	$current_id = $current_user->ID;
	$user_meta = get_userdata( $current_user->ID );
	$user_roles = $user_meta->roles;
	?>
    <div class="listing-ticket__item">
        <div class="ticket">
            <div class="ticket__wrapper">
                <a href="<?php echo esc_url( sts_support_page_url( 'customer-details/?customer_id=' . $content['customer_id'] ) ); ?>"
                   class="ticket__avartar">
                    <img alt="avatar" src="<?php echo esc_url( $content['avatar'] ) ?>" width="80" height="80">
                </a>
                <div class="ticket__content-wrapper">
                    <div class="ticket__content-container">
                        <div class="ticket__content">
                            <div class="ticket__author-top">
                                <a class="ticket__author-name"
                                   href="<?php echo esc_url( sts_support_page_url( 'customer-details/?customer_id=' . $content['customer_id'] ) ); ?>">
									<?php echo esc_html( $content['name'] ); ?>
                                </a>
                                <span class="seperator">|</span>
                                <a href="<?php echo esc_url( $content['theme_link'] ) ?>"
                                   class="ticket__theme">
									<?php echo esc_html( $content['theme_name'] ) ?>
                                </a>
                            </div>
                            <a href="<?php echo esc_url( $content['ticket_link'] ) ?>" class="ticket__subject">
								<?php echo esc_html( $content['subject'] ) ?></a>

                            <div class="ticket__meta">
                                <div class="ticket__meta-item ticket__created-time">
                                    <span class="dashicons dashicons-calendar-alt"></span>
                                    <span>
										<?php if ( $content['status'] == 1 ): ?>
											<?php esc_html_e( 'Latest request ', 'sts' ) ?>
										<?php elseif ( $content['status'] == 2 ): ?>
											<?php esc_html_e( 'Latest responded ', 'sts' ) ?>
										<?php elseif ( $content['status'] == 3 ): ?>
											<?php esc_html_e( 'Close at ', 'sts' ) ?>
										<?php endif; ?>
										<?php echo esc_html( $content['updating_date'] ); ?>
                                    </span>
                                </div>
								<?php if ( $content['numberMessage'] > 0 ): ?>
                                    <div class="ticket__meta-item ticket__number-comment">
                                        <span class="dashicons dashicons-admin-comments"></span>
                                        <span><?php echo esc_html( $content['numberMessage'] ); ?></span>
                                    </div>
								<?php endif; ?>
                                <div class="ticket__meta-item">
									<?php if ( $content['supporter_name'] != '' && $content['status'] != 3 ): ?>
                                        <span class="dashicons dashicons-businessperson"></span>
                                        <span class="supporter-processing"><?php echo esc_html( $content['supporter_name'] ) ?></span>
									<?php endif; ?>
                                </div>
                                <div class="ticket__meta-item"
                                     id="ticket__meta-supporter-<?php echo esc_attr( $content['id'] ) ?>">
									<?php
									if ( isset( $content['user_lock'] ) && $content['user_lock'] != '' ) {
										STS()->get_template( 'tickets/supporter-marking-process.php', array( 'supporter_name' => $content['user_lock'] ) );
									}
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ticket__status">
					<?php
					if ( $content['expired'] == 1 ):
						?>
                        <div class="ticket__expired">
                            <span class="sts-warning"><?php esc_html_e( 'Ticket expired' ) ?></span>
                        </div>
					<?php endif; ?>
                    <div class="ticket__status-content">
						<?php switch ( $content['status'] ) {
							case "1":
								echo '<span class="ticket__status-request">' . esc_html__( 'Request', 'sts' ) . '</span>';
								break;
							case "2":
								echo '<span class="ticket__status-responded">' . esc_html__( 'Responded', 'sts' ) . '</span>';
								break;
							case "3":
								echo '<span class="ticket__status-close">' . esc_html__( 'Close', 'sts' ) . '</span>';
								break;
							default:
								echo '<span class="ticket__status-request">' . esc_html__( 'Request', 'sts' ) . '</span>';
								break;
						}
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;
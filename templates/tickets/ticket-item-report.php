<?php
foreach ( $contents as $content ):
	?>
	<div class="listing-ticket__item">
		<div class="ticket">
			<div class="ticket__wrapper">
				<a href="<?php echo esc_url( sts_support_page_url( 'customer-details/?customer_id=' . $content['customer_id'] ) ); ?>"
				   class="ticket__avartar">
					<img alt="avatar" src="<?php echo esc_url( $content['avatar'] ) ?>">
				</a>
				<div class="ticket__content-wrapper">
					<div class="ticket__content">
						<a class="ticket__author-name"
						   href="<?php echo esc_url( sts_support_page_url( 'customer-details/?customer_id=' . $content['customer_id'] ) ); ?>">
							<?php echo esc_html( $content['name'] ); ?>
						</a>

						<div class="ticket__content-center">
							<a href="<?php echo esc_url( $content['ticket_link'] ) ?>" class="ticket__subject">
								<?php echo esc_html( $content['subject'] ) ?></a>
						</div>
						<div class="ticket__meta">
							<div class="ticket__meta-item ticket__created-time">
								<span class="dashicons dashicons-calendar-alt"></span>
								<span><?php esc_html_e( 'Updated ', 'sts' ) ?><?php echo esc_html( $content['updating_date'] ); ?>
                            </span>
							</div>
							<?php if ( $content['numberMessage'] > 0 ): ?>
								<div class="ticket__meta-item ticket__number-comment">
									<span class="dashicons dashicons-admin-comments"></span>
									<span><?php echo esc_html( $content['numberMessage'] ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="ticket__rating-info">
						<?php echo esc_html( $content['rate_info'] ) ?>
					</div>
				</div>
				<?php if ( $content['rate'] != '' ): ?>
					<div class="ticket__rating">
						<?php switch ( $content['rate'] ) {
							case "1":
								echo '<span class="ticket__rate ticket__rate--satisfied">' . esc_html__( 'Satisfied', 'sts' ) . '</span>';
								break;
							case "0":
								echo '<span class="ticket__rate ticket__rate--unsatisfied">' . esc_html__( 'Unsatisfied', 'sts' ) . '</span>';
								break;
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endforeach;
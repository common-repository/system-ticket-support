<?php
foreach ( $tickets as $ticket ):
	?>
	<div class="ticket">
		<a href="<?php echo esc_url( $ticket['url'] ) ?>"
		   class="ticket__subject">
			<?php echo esc_html( $ticket['subject'] ) ?>
		</a>
		<div class="ticket__meta">
			<div class="ticket__meta-item ticket__created-time">
				<span class="dashicons dashicons-calendar-alt"></span>
				<span><?php esc_html_e( 'Updated ', 'sts' ) ?>
					<?php echo esc_html( $ticket['updating_date'] ); ?>
				</span>
			</div>
		</div>
	</div>
<?php
endforeach;

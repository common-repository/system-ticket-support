<?php foreach ( $notes as $note ): ?>
	<div class="note">
		<div class="note__content">
			<a href="<?php echo esc_url( $note['url'] ) ?>" target="_blank"
			   class="note__message"><?php echo wp_kses_post( $note['message'] ); ?></a>
		</div>
		<div class="note__meta">
			<span class="note__meta-item note__meta-item--date">
				<span class="dashicons dashicons-calendar-alt"></span><?php echo esc_html( date( 'F d,Y', strtotime( $note['created_date'] ) ) ); ?>
			</span>
		</div>
	</div>
<?php endforeach; ?>

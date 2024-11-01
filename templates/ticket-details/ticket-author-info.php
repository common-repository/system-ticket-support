<div class="single-ticket__author-info">
	<a class="single-ticket__author-name" href="<?php echo esc_url( $profile_url ) ?>">
		<?php echo esc_html( $name ); ?>
	</a>
	<span class="single-ticket__meta-time">
		<?php echo sts_time_ago( strtotime( $created_date ) ); ?>
	</span>
	<?php if ( $is_question == 0 ): ?>
		<a class="single-ticket__hastag" href="<?php echo esc_url( $message_url ) ?>">
			<span class="dashicons dashicons-admin-links"></span>
		</a>
	<?php endif; ?>
</div>
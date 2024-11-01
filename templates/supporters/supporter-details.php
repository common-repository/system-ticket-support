<div class="sts-page-content-main">
	<div class="sts-page-profile-header">
		<span class="sts-page-profile-header-item"><?php esc_html_e( 'Supporter information', 'sts' ) ?></span>
	</div>

	<div class="supporter-information">
		<div class="supporter-information-item">
			<div class="supporter-information-label"><?php esc_html_e( 'Name', 'sts' ) ?></div>
			<div class="supporter-information-value"><?php echo esc_html( $supporter_name ) ?></div>
		</div>
		<div class="supporter-information-item">
			<div class="supporter-information-label"><?php esc_html_e( 'Email address:', 'sts' ) ?></div>
			<div class="supporter-information-value"><?php echo esc_html( $supporter_mail ) ?></div>
		</div>
		<?php if ( $signature != '' ): ?>
			<div class="supporter-information-item">
				<div class="supporter-information-label"><?php esc_html_e( 'Signature:', 'sts' ) ?></div>
				<div class="supporter-information-value"><?php echo wp_kses_post( $signature ) ?></div>
			</div>
		<?php endif; ?>
	</div>
</div>
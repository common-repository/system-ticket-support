<div class="sts-user-container">
	<a href="javascript:history.back()" class="button-back"><span class="dashicons dashicons-arrow-left-alt"></span></a>
	<div class="sts-user-avatar">
		<img src="<?php echo esc_url( $avatar_url ) ?>"
		     alt="<?php esc_attr_e( 'Avatar image', 'sts' ) ?>">
	</div>
	<div class="sts-user-content">
		<div class="sts-user-name"><?php echo esc_html( $name ) ?></div>
		<div class="sts-user-mail"><?php echo esc_html( $email ) ?></div>
	</div>
</div>
<?php
if ( isset( $_GET['key_reset'] ) && wp_verify_nonce( $_GET['key_reset'], 'sts_lost_password_security' ) ):
	if ( isset( $_GET['email'] ) && $_GET['email'] != '' ):
		$email = sanitize_email( $_GET['email'] );
		?>
        <div class="sts-page-main mt-30">
            <div class="form-wrapper form-wrapper-password">
                <div class="form-content">
                    <h3 class="form-title"><?php esc_html_e( "Reset password", "sts" ) ?></h3>
                    <div class="form__message">
		                <?php
		                echo wp_kses_post( STS()->notice()->display_notice() );
		                ?>
                    </div>
                    <form class="form form-lost-password" method="post" action="">
                        <input type="hidden" name="email" value="<?php echo esc_attr( $email ) ?>">
                        <div class="form-control-group">
                            <label for="password" class="form-control-label">
								<?php esc_html_e( "Password*", "sts" ) ?>
                            </label>
                            <input class="form-control" id="password" name="password" type="password" required>
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-admin-network"></span>
                            </div>
                        </div>
                        <div class="form-control-group">
                            <label for="rppassword" class="form-control-label">
								<?php esc_html_e( "Repeat password*", "sts" ) ?>
                            </label>
                            <input id="rppassword" class="form-control" name="rppassword" type="password" required>
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-admin-network"></span>
                            </div>
                        </div>
                        <div class="form-control-group">
							<?php do_action( 'resetpass_form', wp_get_current_user() ); ?>
                        </div>
                        <div class="form-button">
							<?php wp_nonce_field( 'processing-reset-password', 'sts_reset_password_nonce_field' ); ?>
                            <button class="btn btn-primary" type="submit">
								<?php esc_html_e( "Create new password", "sts" ) ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	<?php
	endif;
else: ?>
    <p class="sts-no-url">
		<?php esc_html_e( 'This url is expired. Please visit this link, we will send to you a mail to get url.', 'sts' ) ?>
        <a href="<?php echo esc_url( sts_support_page_url( 'lost-password' ) ) ?>" class="link-forget-password">
			<?php esc_html_e( 'Forgot Password', 'sts' ) ?>
        </a>
    </p>
<?php endif; ?>


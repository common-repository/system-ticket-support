<?php
$current_user = wp_get_current_user();
if ( ! $current_user->exists() ):
	?>
    <div class="sts-page-main mt-30">
        <div class="form-wrapper form-wrapper-lost-password">
            <div class="form-content">
                <div class="form__message">
					<?php
					echo wp_kses_post( STS()->notice()->display_notice() );
					?>
                </div>
                <div class="reset-password-text">
					<?php esc_html_e( 'Please enter your username or e-mail address. You will receive a link to create a new password via e-mail.', 'sts' ) ?>
                </div>
                <form class="form form-lost-password" method="post" action=""
                      name="lostpasswordform" id="lostpasswordform">
                    <div class="form-control-group">
                        <label for="user_login" class="form-control-label">
							<?php esc_html_e( "Username or Email address*", "sts" ) ?>
                        </label>
                        <input class="form-control" id="user_login" name="user_login" type="text" required>
                        <div class="form-control-group-icon">
                            <span class="dashicons dashicons-email"></span>
                        </div>
                    </div>
                    <div class="form-control-group">
						<?php do_action( 'lostpassword_form' ); ?>
                    </div>
                    <div class="form-button">
						<?php wp_nonce_field( 'processing-lost-password', 'sts_lost_password_nonce_field' ); ?>
                        <button class="btn btn-primary" type="submit">
							<?php esc_html_e( "Get reset link", "sts" ) ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
endif;


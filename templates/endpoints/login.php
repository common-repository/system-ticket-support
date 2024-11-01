<div class="container-fluid">
    <div class="form-wrapper form-wrapper-login">
        <div class="form-content">
			<?php
			do_action( 'sts_login_before','Login by envato' );
			?>
            <h3 class="form-title"><?php esc_html_e( "Login to your account", "sts" ) ?></h3>
            <div class="form__message">
				<?php
				echo wp_kses_post( STS()->notice()->display_notice() );
				?>
            </div>
			<?php $login = ( isset( $_GET['login'] ) ) ? sanitize_text_field( $_GET['login'] ) : 0;
			if ( $login === "locked" ) : ?>
                <div class="form__message form__message--error"><?php esc_html_e( 'Your account has locked!', 'sts' ) ?></div>
			<?php
			endif;
			if ( isset( $_GET['api_status'] ) ):
				?>
                <div class="form__message form__message--error"><?php esc_html_e( 'Error status: ', 'sts' ) ?><?php echo esc_html( $_GET['status'] ) ?></div>
			<?php endif; ?>
            <div class="login-form">
                <form class="form form-login" name="loginform" id="<?php echo esc_attr( $args['form_id'] ) ?>" action=""
                      method="post">
					<?php wp_nonce_field( 'processing-login', 'sts_login_nonce_field' ); ?>
                    <div class="form-control-group">
						<?php echo wp_kses_post( $login_form_top ); ?>
                    </div>
                    <div class="form-control-group">
                        <label for="<?php echo esc_attr( $args['id_username'] ) ?>" class="form-control-label">
							<?php echo esc_html( $args['label_username'] ) ?>
                        </label>
                        <input class="form-control" id="<?php echo esc_attr( $args['id_username'] ) ?>" name="log"
                               type="text" required value="<?php echo esc_attr( $args['value_username'] ) ?>">
                        <div class="form-control-group-icon">
                            <span class="dashicons dashicons-email"></span>
                        </div>
                    </div>
                    <div class="form-control-group">
                        <label for="<?php echo esc_attr( $args['id_password'] ) ?>" class="form-control-label">
							<?php echo esc_html( $args['label_password'] ) ?>
                        </label>
                        <input class="form-control" id="<?php echo esc_attr( $args['id_password'] ) ?>" name="pwd"
                               type="password" required>
                        <div class="form-control-group-icon">
                            <span class="dashicons dashicons-admin-network"></span>
                        </div>
                    </div>
                    <div class="form-control-group">
						<?php echo wp_kses_post( $login_form_middle ); ?>
                    </div>
                    <div class="form-remember-password">
                        <div class="checkbox">
                            <label for="<?php echo esc_attr( $args['id_remember'] ) ?>">
                                <input id="<?php echo esc_attr( $args['id_remember'] ) ?>" type="checkbox"
                                       name="rememberme" value="<?php echo esc_attr( $args['value_remember'] ) ?>">
								<?php echo esc_html( $args['label_remember'] ) ?>
                            </label>
                        </div>
                        <a href="<?php echo esc_url( sts_support_page_url( 'lost-password' ) ) ?>"
                           class="link-forget-password">
							<?php esc_html_e( 'Forgot password?', 'sts' ) ?>
                        </a>
                    </div>
                    <div class="form-control-group">
						<?php echo wp_kses_post( $login_form_bottom ); ?>
                    </div>
                    <div class="form-login-button">
                        <button class="btn btn-primary btn-lg" id="<?php echo esc_attr( $args['id_submit'] ) ?>"
                                name="wp-submit" type="submit"><?php echo esc_html( $args['label_log_in'] ) ?></button>
                    </div>
					<?php if ( isset( $_GET['sts_redirect'] ) ):
						$current_url = sanitize_text_field( $_GET['sts_redirect'] );
						?>
                        <input type="hidden" name="redirect_url" id="sts-redirect"
                               value="<?php echo esc_attr( rawurldecode( $current_url ) ) ?>">
					<?php endif; ?>
                </form>
            </div>
			<?php
			$is_can_register = get_option( 'users_can_register' );
			if ( $is_can_register == 1 ):
				?>
                <div class="form-login-register">
                    <span><?php esc_html_e( "Don't have an account?", "sts" ) ?></span>
                    <a href="<?php echo esc_url( sts_support_page_url( 'register' ) ) ?>"><?php esc_html_e( "Create an account", "sts" ) ?></a>
                </div>
			<?php
			endif;
			?>
        </div>
    </div>
</div>
<?php
STS()->get_template( 'sts-footer.php' );
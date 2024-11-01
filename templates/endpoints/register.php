<?php $current_user = wp_get_current_user();
if ( ! $current_user->exists() ):
	?>
    <div class="container-fluid">
        <div class="form-wrapper form-wrapper-register">
            <div class="form-content">
				<?php
				do_action( 'sts_register_before' );
				?>
                <h3 class="form-title"><?php esc_html_e( "Register", "sts" ) ?></h3>
                <div class="form__message">
					<?php
					$messages = STS()->notice()->display_notice();
					if ( $messages != '' ):
						foreach ( $messages as $message ):
							?>
                            <div class="form__message--error">
								<?php
								echo wp_kses_post( $message['message'] );
								?>
                            </div>
						<?php
						endforeach;
					endif;
					?>
                </div>
                <form class="form form-register" method="post" action="" id="registerform" name="registerform">
                    <div class="form-group">
                        <div class="form-control-group">
                            <label for="firstName" class="form-control-label">
								<?php esc_html_e( "First name*", "sts" ) ?>
                            </label>
                            <input class="form-control" name="firstName" id="firstName" type="text" required
                                   value="<?php echo esc_attr( isset( $_POST['firstName'] ) ? $_POST['firstName'] : '' ) ?>">
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-businessperson"></span>
                            </div>
                        </div>
                        <div class="form-control-group">
                            <label for="lastName" class="form-control-label">
								<?php esc_html_e( "Last name*", "sts" ) ?>
                            </label>
                            <input class="form-control" id="lastName" name="lastName" type="text" required
                                   value="<?php echo esc_attr( isset( $_POST['lastName'] ) ? $_POST['lastName'] : '' ) ?>">
                            <div class="form-control-group-icon">
                                <span class="dashicons dashicons-businessperson"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-control-group">
                        <label for="email" class="form-control-label">
							<?php esc_html_e( "Email address*", "sts" ) ?>
                        </label>
                        <input class="form-control" id="email" name="email" type="email" required
                               value="<?php echo esc_attr( isset( $_POST['email'] ) ? $_POST['email'] : '' ) ?>"
                        >
                        <div class="form-control-group-icon">
                            <span class="dashicons dashicons-email"></span>
                        </div>
                        <span class="form-control-message" id="form-control-message-email"></span>
                    </div>
                    <div class="form-group">
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
                            <span class="form-control-message" id="form-control-message-password"></span>
                        </div>
                    </div>
					<?php
					do_action( 'sts_register_middle' );
					?>
                    <div class="form-control-group">
						<?php do_action( 'register_form' ); ?>
                    </div>
                    <div class="form-button">
						<?php wp_nonce_field( 'processing-register', 'sts_register_nonce_field' ); ?>
                        <button class="btn btn-primary btn-lg" type="submit">
							<?php esc_html_e( "Register", "sts" ) ?>
                        </button>
                        <div class="mt-3 policy-text">
							<?php
							echo wpautop( wp_kses_post( get_option( 'sts_policy_register', '' ) ) );
							?>
                        </div>
                    </div>
                </form>
                <div class="form-login-register">
                <span>
                    <?php esc_html_e( "Already have an account?", "sts" ) ?>
                </span>
                    <a href="<?php echo esc_url( sts_support_page_url( 'login/' ) ) ?>">
						<?php esc_html_e( "Login", "sts" ) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif;
STS()->get_template( 'sts-footer.php' );


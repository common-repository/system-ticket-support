<?php
$current_user = wp_get_current_user();
$is_lock      = 0;
if ( $current_user->exists() ) {
	$is_lock = get_user_meta( $current_user->ID, 'sts_is_lock', true );
}
?>
    <div class="form-wrapper form-wrapper-submit-ticket <?php if ( ! $current_user->exists() ) {
		echo 'form-wrapper-submit-ticket-no-login';
	} ?>">
        <div class="form-content">
            <h3 class="form-title"><?php esc_html_e( "Submit ticket", "sts" ) ?></h3>
            <div class="form__message"></div>
            <form class="form form-submit-ticket" method="post" action=""
                  data-sts-form-action="sts_process_submit_ticket" data-sts-ladda="true"
                  data-sts-callback="STS.redirectAfterAlert"
                  enctype="multipart/form-data">
                <div class="form-section">
					<?php
					do_action( 'sts_submit_ticket_before' )
					?>
                </div>
                <div class="form-container close" id="form-container">
					<?php
					if ( ! $current_user->exists() || $is_lock == 1 ):?>
                        <div class="form-section">
                            <div class="form-section-title">
								<?php esc_html_e( "Registration:", "sts" ) ?>
                            </div>
                            <div class="form-group">
                                <div class="form-control-group">
                                    <label for="firstName" class="form-control-label">
										<?php esc_html_e( "First name*", "sts" ) ?>
                                    </label>
                                    <input class="form-control" name="firstName" id="firstName" type="text" required>
                                    <div class="form-control-group-icon">
                                        <span class="dashicons dashicons-businessperson"></span>
                                    </div>
                                </div>
                                <div class="form-control-group">
                                    <label for="lastName" class="form-control-label">
										<?php esc_html_e( "Last name*", "sts" ) ?>
                                    </label>
                                    <input class="form-control" id="lastName" name="lastName" type="text" required>
                                    <div class="form-control-group-icon">
                                        <span class="dashicons dashicons-businessperson"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control-group">
                                <label for="email" class="form-control-label">
									<?php esc_html_e( "Email address*", "sts" ) ?>
                                </label>
                                <input class="form-control" id="email" name="email" type="email" required>
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
                                    <input id="rppassword" class="form-control" name="rppassword" type="password"
                                           required>
                                    <div class="form-control-group-icon">
                                        <span class="dashicons dashicons-admin-network"></span>
                                    </div>
                                    <span class="form-control-message" id="form-control-message-password"></span>
                                </div>
                            </div>
							<?php
							do_action( 'sts_submit_ticket_middle_first' );
							?>
                        </div>
					<?php endif ?>
					<?php if ( $current_user->exists() ): ?>
						<?php
						do_action( 'sts_submit_ticket_middle_second' );
						?>
					<?php endif; ?>
                    <div class="form-section">
                        <div class="form-section-title">
							<?php esc_html_e( "Ticket information:", "sts" ) ?>
                        </div>
                        <div class="form-control-group">
                            <label for="ticketSubject" class="form-control-label">
								<?php esc_html_e( "Ticket subject*", "sts" ) ?>
                            </label>
                            <input id="ticketSubject" class="form-control" name="ticketSubject" type="text" required>
                        </div>
                        <div class="sts-website-support">
							<?php
							do_action( 'sts_submit_ticket_middle_third' );
							?>
                        </div>
                        <div class="form-control-group form-control-editor">
                            <textarea name="ticketMessage" id="ticketmessage"
                                      class="sts-text-editor form-control"></textarea>
                        </div>
                        <div class="form-control-group">
                            <label class="form-label-attachment">
                                <span class="dashicons dashicons-paperclip"></span>
								<?php esc_html_e( "Add attachment( jpg, jpeg, png, gif allowed):", "sts" ) ?>
                            </label>
                            <input type="file" name="attachment[]" id="attachment" multiple>
                            <span class="form-control-message" id="form-control-message-file"></span>
                        </div>
                        <div class="form-control-group">
							<?php do_action( 'register_form' ); ?>
                        </div>
						<?php wp_nonce_field( 'processing-submit-ticket', 'sts_submit_ticket_field' ); ?>
                        <div class="form-button">
                            <button class="btn btn-primary" type="submit">
								<?php esc_html_e( "Submit ticket", "sts" ) ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php



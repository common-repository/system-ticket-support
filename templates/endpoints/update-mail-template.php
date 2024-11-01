<?php
$id           = sanitize_text_field( $_GET['id'] );
$mail_subject = STS()->db()->mail_template()->get_mail_template_by_id( $id );
if ( $mail_subject ):
	?>
    <div class="sts-page-main">
        <div class="sts-page-banner">
            <div class="container-fluid">
                <div class="sts-page-banner-content">
                    <h3 class="sts-page-title">
						<?php
						esc_html_e( 'Update mail template', 'sts' )
						?>
                    </h3>
                </div>
                <div class="form__message"></div>
            </div>
        </div>
        <div class="sts-page-container">
            <div class="container-fluid">
                <div class="sts-page-content-main">
                    <div class="sts-page-profile-header">
                        <span class="sts-page-profile-header-item"><?php esc_html_e( 'Mail template information', 'sts' ) ?></span>
                    </div>
                    <div class="page-form">
                        <form action="" method="post" id="form-update-subject-mail"
                              data-sts-form-action="sts_update_mail_subject" class="form form-mail-template"
                              data-sts-ladda="true">
							<?php wp_nonce_field( 'processing-update-subject', 'sts_update_subject_mail_nonce_field' ); ?>
                            <div class="form-control-group">
                                <label for="subject-value" class="form-control-label">
									<?php esc_html_e( 'Subject*', 'sts' ) ?>
                                </label>
                                <input id="subject-value" class="form-control" name="subject_value" type="text"
                                       value="<?php echo esc_html( $mail_subject->subject ) ?>" required>
                                <div class="form-control-group-icon">
                                    <span class="dashicons dashicons-thumbs-up"></span>
                                </div>
                            </div>
                            <div class="form-control-group">
                                <label class="form__label" for="mailContent">
									<?php esc_html_e( 'Mail content*:', 'sts' ) ?>
                                </label>
                                <textarea name="mailContent" id="mailContent"
                                          class="sts-text-editor form-control"><?php echo wpautop( wp_kses_post( $mail_subject->content ) ) ?></textarea>
                            </div>
                            <input type="hidden" name="id" value="<?php echo esc_attr( $mail_subject->id ) ?>">
                            <div class="form-button-group">
                                <div class="form-button-group-item">
                                    <button class="btn btn-primary" type="submit">
										<?php esc_html_e( 'Update mail template', 'sts' ) ?>
                                    </button>
                                </div>
                                <div class="form-button-group-item">
                                    <a href="<?php echo esc_url( sts_support_page_url( 'mail-templates' ) ) ?>"
                                       class="btn btn-default">
										<?php esc_html_e( 'Come back', 'sts' ) ?>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
endif;
